<?php
require_once __DIR__ . '/nz-auth.php';
nz_auth_start_session();

$base = nz_base_path();
$homePath      = $base . '/index.php';
$loginPath     = $base . '/login.php';
$registerPath  = $base . '/register.php';
$dashboardPath = $base . '/verified-visa.php';

if (nz_auth_is_logged_in()) {
    header('Location: ' . $dashboardPath);
    exit;
}

$errorMessage   = '';
$successMessage = '';
$familyName     = '';
$givenNames     = '';
$email          = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $familyName      = trim((string) ($_POST['family_name'] ?? ''));
    $givenNames      = trim((string) ($_POST['given_names'] ?? ''));
    $email           = trim((string) ($_POST['email'] ?? ''));
    $password        = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    $result = nz_auth_register($familyName, $givenNames, $email, $password, $confirmPassword);

    if ($result['ok'] === true) {
        $successMessage = (string) $result['message'];
        $familyName = $givenNames = $email = '';
    } else {
        $errorMessage = (string) $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create an Account - Visa Verification Service - New Zealand Immigration</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;color:#2b2b2b;background:#f4f7fa;min-height:100vh;display:flex;flex-direction:column}

.nz-header{background:#fff;border-bottom:1px solid #ddd;padding:0 20px}
.nz-header-inner{max-width:1140px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;min-height:72px;gap:16px;flex-wrap:wrap}
.nz-logo{display:flex;align-items:center;gap:12px;text-decoration:none;color:#1a1a1a}
.nz-logo svg{width:50px;height:38px}
.nz-logo-text{font-size:15px;font-weight:600;line-height:1.3;color:#1a1a1a}
.nz-logo-text span{display:block;font-size:12px;font-weight:400;color:#555}

.nz-nav{background:#005b99;padding:0 20px}
.nz-nav-inner{max-width:1140px;margin:0 auto;display:flex;gap:0}
.nz-nav a{color:#fff;text-decoration:none;padding:12px 18px;font-size:14px;font-weight:500}

.reg-container{max-width:520px;margin:50px auto;padding:0 20px;flex:1}
.reg-card{background:#fff;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.07);padding:36px 32px}
.reg-card h1{font-size:24px;font-weight:700;color:#1a1a1a;margin:0 0 6px}
.reg-card .subtitle{font-size:14px;color:#555;margin:0 0 24px}

.field{margin-bottom:18px}
.field label{display:block;font-size:13px;font-weight:600;color:#1a3a5c;margin-bottom:6px}
.field input{width:100%;padding:11px 14px;border:1px solid #c5cdd8;border-radius:5px;font-size:14px;outline:none;transition:border-color .2s}
.field input:focus{border-color:#005b99;box-shadow:0 0 0 3px rgba(0,91,153,.12)}
.field .hint{font-size:12px;color:#777;margin-top:4px}

.row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}

.btn-submit{width:100%;padding:13px;background:#005b99;color:#fff;border:none;border-radius:5px;font-size:15px;font-weight:600;cursor:pointer;transition:background .2s}
.btn-submit:hover{background:#004a80}

.error-box{margin-bottom:16px;padding:12px 14px;background:#fdecea;border:1px solid #f4c7c3;border-radius:5px;color:#b42318;font-size:14px}
.success-box{margin-bottom:16px;padding:12px 14px;background:#e6f4ea;border:1px solid #b7e1c8;border-radius:5px;color:#1e7e34;font-size:14px}

.meta{margin-top:16px;font-size:14px;color:#555;text-align:center}
.meta a{color:#005b99;font-weight:600;text-decoration:none}
.meta a:hover{text-decoration:underline}

.nz-footer{background:#082a3e;padding:20px;text-align:center;margin-top:auto}
.nz-footer span{color:#7aa8cc;font-size:12px}

@media(max-width:520px){
    .reg-card{padding:24px 18px}
    .row-2{grid-template-columns:1fr}
}
</style>
</head>
<body>

<header class="nz-header">
    <div class="nz-header-inner">
        <a href="<?php echo htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8'); ?>" class="nz-logo">
            <svg viewBox="0 0 60 45" xmlns="http://www.w3.org/2000/svg">
                <rect width="60" height="45" fill="#00247d"/>
                <g fill="#cc142b">
                    <polygon points="37,11 38.5,16 43.5,16 39.5,19 41,24 37,21 33,24 34.5,19 30.5,16 35.5,16"/>
                    <polygon points="47,18 48,21 51,21 48.5,23 49.5,26 47,24 44.5,26 45.5,23 43,21 46,21"/>
                    <polygon points="47,6 48,9 51,9 48.5,11 49.5,14 47,12 44.5,14 45.5,11 43,9 46,9"/>
                    <polygon points="43,30 44,33 47,33 44.5,35 45.5,38 43,36 40.5,38 41.5,35 39,33 42,33"/>
                </g>
            </svg>
            <div class="nz-logo-text">
                NEW ZEALAND GOVERNMENT
                <span>Immigration New Zealand</span>
            </div>
        </a>
    </div>
</header>

<nav class="nz-nav">
    <div class="nz-nav-inner">
        <a href="<?php echo htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8'); ?>">Home</a>
    </div>
</nav>

<div class="reg-container">
    <div class="reg-card">
        <h1>Create an account</h1>
        <p class="subtitle">Set up your Visa Verification Service account</p>

        <?php if ($errorMessage !== ''): ?>
            <div class="error-box"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($successMessage !== ''): ?>
            <div class="success-box"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
                <a href="<?php echo htmlspecialchars($loginPath, ENT_QUOTES, 'UTF-8'); ?>" style="color:#1e7e34;font-weight:700;">Log in now &rarr;</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($registerPath, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="row-2">
                <div class="field">
                    <label for="family_name">Family name *</label>
                    <input type="text" id="family_name" name="family_name" required
                        value="<?php echo htmlspecialchars($familyName, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="field">
                    <label for="given_names">Given names</label>
                    <input type="text" id="given_names" name="given_names"
                        value="<?php echo htmlspecialchars($givenNames, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>
            <div class="field">
                <label for="email">Email address *</label>
                <input type="email" id="email" name="email" required autocomplete="email"
                    value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="field">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
                <div class="hint">Minimum 6 characters</div>
            </div>
            <div class="field">
                <label for="confirm_password">Confirm password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn-submit">Create account</button>
        </form>

        <p class="meta">
            Already have an account? <a href="<?php echo htmlspecialchars($loginPath, ENT_QUOTES, 'UTF-8'); ?>">Log in</a>
        </p>
    </div>
</div>

<footer class="nz-footer">
    <span>Te Kāwanatanga o Aotearoa &mdash; New Zealand Government &middot; Crown copyright &copy; <?php echo date('Y'); ?></span>
</footer>

</body>
</html>
