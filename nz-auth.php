<?php

require_once __DIR__ . '/api/config.php';

function nz_auth_start_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function nz_auth_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        nz_mysql_host(),
        nz_mysql_port(),
        nz_mysql_db(),
        nz_mysql_charset()
    );

    $pdo = new PDO($dsn, nz_mysql_user(), nz_mysql_pass(), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    nz_auth_ensure_table($pdo);
    return $pdo;
}

function nz_auth_ensure_table(PDO $pdo): void
{
    $sql = "CREATE TABLE IF NOT EXISTS `nz_users` (
        `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `family_name`   VARCHAR(191) NOT NULL,
        `given_names`   VARCHAR(191) NOT NULL DEFAULT '',
        `email`         VARCHAR(191) NOT NULL,
        `password_hash` VARCHAR(255) NOT NULL,
        `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uniq_nz_users_email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sql);
}

function nz_auth_normalize_email(string $email): string
{
    return strtolower(trim($email));
}

function nz_auth_register(string $familyName, string $givenNames, string $email, string $password, string $confirmPassword): array
{
    $familyName = trim($familyName);
    $givenNames = trim($givenNames);
    $email      = nz_auth_normalize_email($email);

    if ($familyName === '') {
        return ['ok' => false, 'message' => 'Family name is required.'];
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Please provide a valid email address.'];
    }
    if (strlen($password) < 6) {
        return ['ok' => false, 'message' => 'Password must be at least 6 characters.'];
    }
    if ($password !== $confirmPassword) {
        return ['ok' => false, 'message' => 'Passwords do not match.'];
    }

    $pdo = nz_auth_db();

    $stmt = $pdo->prepare('SELECT id FROM `nz_users` WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        return ['ok' => false, 'message' => 'This email is already registered.'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $pdo->prepare('INSERT INTO `nz_users` (family_name, given_names, email, password_hash) VALUES (:family_name, :given_names, :email, :password_hash)');
    $insert->execute([
        ':family_name'   => $familyName,
        ':given_names'   => $givenNames,
        ':email'         => $email,
        ':password_hash' => $hash,
    ]);

    return ['ok' => true, 'message' => 'Account created successfully. Please log in.'];
}

function nz_auth_login(string $identifier, string $password): array
{
    $identifier = trim($identifier);

    if ($identifier === '') {
        return ['ok' => false, 'message' => 'Please enter your username or email.'];
    }

    $pdo = nz_auth_db();

    // Try by email first, then by family_name (username)
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare('SELECT id, email, password_hash, family_name, given_names, created_at FROM `nz_users` WHERE email = :val LIMIT 1');
    } else {
        $stmt = $pdo->prepare('SELECT id, email, password_hash, family_name, given_names, created_at FROM `nz_users` WHERE family_name = :val LIMIT 1');
    }
    $stmt->execute([':val' => filter_var($identifier, FILTER_VALIDATE_EMAIL) ? strtolower($identifier) : $identifier]);
    $user = $stmt->fetch();

    if (!is_array($user) || !password_verify($password, (string) $user['password_hash'])) {
        return ['ok' => false, 'message' => 'Invalid username/email or password.'];
    }

    nz_auth_start_session();
    $_SESSION['nz_user_id']          = (int) $user['id'];
    $_SESSION['nz_user_email']       = (string) $user['email'];
    $_SESSION['nz_user_family_name'] = (string) $user['family_name'];
    $_SESSION['nz_user_given_names'] = (string) $user['given_names'];
    $_SESSION['nz_user_created_at']  = (string) $user['created_at'];

    return ['ok' => true, 'message' => 'Login successful.'];
}

function nz_auth_logout(): void
{
    nz_auth_start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
}

function nz_auth_is_logged_in(): bool
{
    nz_auth_start_session();
    return isset($_SESSION['nz_user_id']) && $_SESSION['nz_user_id'] > 0;
}

function nz_auth_user_name(): string
{
    nz_auth_start_session();
    $given  = trim((string) ($_SESSION['nz_user_given_names'] ?? ''));
    $family = trim((string) ($_SESSION['nz_user_family_name'] ?? ''));
    return trim($given . ' ' . $family);
}

function nz_auth_user_email(): string
{
    nz_auth_start_session();
    return (string) ($_SESSION['nz_user_email'] ?? '');
}

function nz_base_path(): string
{
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $scriptDir = $scriptDir === '.' ? '/' : $scriptDir;
    return $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
}
