<?php
require_once __DIR__ . '/nz-auth.php';
nz_auth_start_session();

$base = nz_base_path();
$loginPath    = $base . '/login.php';
$registerPath = $base . '/register.php';
$dashboardPath = $base . '/verified-visa.php';
$logoutPath   = $base . '/logout.php';
$loggedIn = nz_auth_is_logged_in();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Visa Verification Service - New Zealand Immigration</title>
    <style>
        :root {
            /* White & Background */
            --white: #fff;
            --bg: #fff;

            /* Text Colors */
            --text: #2b2b2b;
            --text-dark: #1a1a1a;
            --text-body: #333;
            --text-muted: #555;
            --text-light: #666;
            --text-lighter: #888;
            --text-faint: #aaa;

            /* Primary / Blue */
            --primary: #005b99;
            --primary-dark: #004a80;
            --primary-darker: #003d66;

            /* Button Blue */
            --btn-blue: #0e7ac3;
            --btn-blue-hover: #1d4ed8;

            /* Info Bar (Orange) */
            --info-bar-bg: #d64000;
            --info-bar-hover: #ffe0c0;
            --info-icon-color: #fff;

            /* Borders */
            --border: #ddd;
            --border-light: #eee;
            --border-nav: #e5e7eb;

            /* Breadcrumb */
            --breadcrumb-text: #1a3a5c;
            --breadcrumb-bg: #e8ecf0;
            --breadcrumb-hover: #d0d8e0;

            /* Surface / Light Backgrounds */
            --surface-light: #f0f6fc;
            --surface-input-border: #3a6d8c;

            /* Footer */
            --footer-bg: #323849;
            --footer-dark: #082a3e;
            --footer-text: #b3d4fc;
            --footer-copy: #7aa8cc
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text);
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column
        }

        /* === TOP INFO BAR === */
        .info-bar {
            background: var(--info-bar-bg);
            color: var(--white);
            padding: 28px 20px 24px;
            line-height: 1.6
        }

        .info-bar-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: flex;
            gap: 24px;
            align-items: flex-start;
            flex-wrap: wrap
        }

        .info-bar-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--info-icon-color);
            margin-top: 4px
        }

        .info-bar-icon svg {
            width: 100%;
            height: 100%
        }

        .info-bar-icon .cls-1 {
            fill: var(--info-icon-color)
        }

        .info-bar-left {
            flex: 0 0 290px
        }

        .info-bar-left h2 {
            font-size: 27px;
            font-weight: 700;
            line-height: 1.25;
            margin: 0 20px 8px 0;
            color: var(--white)
        }

        .info-bar-left .info-date {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: rgba(255, 255, 255, .8);
            font-weight: 600
        }

        .info-bar-right {
            flex: 1;
            font-size: 16px;
            line-height: 1.6;
            color: var(--white)
        }

        .info-bar-right a {
            color: var(--white);
            text-decoration: underline;
            font-weight: 600
        }

        .info-bar-right a:hover {
            color: var(--info-bar-hover)
        }

        /* === HEADER === */
        .nz-header {
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            padding: 0 20px
        }

        .nz-header-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 90px;
            gap: 20px;
            flex-wrap: wrap
        }

        .nz-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--text-dark)
        }

        .nz-logo-fern {
            width: 80px;
            height: 62px
        }

        .nz-logo-text {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.25;
            color: var(--text-dark);
            letter-spacing: .02em;
            text-transform: uppercase
        }

        .nz-logo-text span {
            display: block;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: .01em
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 24px
        }

        .header-actions a {
            font-size: 16px;
            color: var(--text-body);
            text-decoration: none;
            font-weight: 500
        }

        .header-actions a:hover {
            color: #004a80;
        }

        .btn-login {
            display: inline-block;
            padding: 12px 32px;
            background: var(--btn-blue);
            color: var(--white) !important;
            border-radius: 8px;
            font-weight: 700;
            font-size: 17px;
            text-decoration: none !important;
            letter-spacing: .02em;
            transition: background .2s
        }

        .btn-login:hover {
            background: var(--btn-blue-hover)
        }

        /* === NAV === */
        .nz-nav {
            background: var(--bg);
            border-bottom: 2px solid var(--border-nav);
            padding: 0 20px
        }

        .nz-nav-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: flex;
            gap: 0;
            align-items: center;
            justify-content: space-between
        }

        .nz-nav-links {
            display: flex;
            gap: 0
        }

        .nz-nav-links a {
            color: var(--text-dark);
            text-decoration: none;
            padding: 16px 24px;
            font-size: 17px;
            font-weight: 700;
            border-bottom: 3px solid transparent;
            transition: border-color .2s;
            display: flex;
            align-items: center;
            gap: 6px
        }

        .nz-nav-links a .maori {
            font-weight: 400;
            color: var(--text-light);
            font-size: 17px
        }

        .nz-nav-links a .arrow {
            font-size: 12px;
            color: var(--text-lighter);
            margin-left: 2px
        }

        .nz-nav-links a:hover {
            border-bottom-color: var(--primary);

        }

        .nz-nav-search {
            padding: 16px 0;
            color: var(--text-dark);
            cursor: pointer;
            display: flex;
            align-items: center
        }

        /* === BREADCRUMB === */
        .breadcrumb {
            width: 1140px;
            padding: 0 20px 60px;
            margin: 0 auto;

            padding: 18px 20px;
            font-size: 14px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap
        }

        .breadcrumb a {
            color: var(--breadcrumb-text);
            text-decoration: none;
            background: var(--breadcrumb-bg);
            padding: 5px 14px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 500;
            transition: background .15s
        }

        .breadcrumb a:hover {
            background: var(--breadcrumb-hover)
        }

        .breadcrumb .sep {
            color: var(--text-faint);
            font-size: 20px
        }

        .breadcrumb .current {
            font-size: 14px;
            color: var(--text-muted);
            padding-left: 2px
        }

        /* === CONTENT LAYOUT === */
        .content-wrap {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 20px 60px;
            display: flex;
            gap: 100px;
            flex-wrap: wrap
        }

        .sidebar {
            width: 260px;
            flex-shrink: 0
        }

        .main-content {
            flex: 1;
            min-width: 0
        }

        /* === SIDEBAR === */
        .sidebar-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            padding: 10px 0;
            border-bottom: 1px solid var(--primary);
            margin-bottom: 6px
        }

        .sidebar-nav {
            list-style: none;
            padding: 0
        }

        .sidebar-nav li {
            border-bottom: 1px solid var(--border-light)
        }

        .sidebar-nav li a {
            display: block;
            padding: 10px 12px;
            color: var(--text);
            text-decoration: none;
            font-size: 17px;
            transition: background .15s
        }

        .sidebar-nav li a:hover {
            /* background: var(--border) */

        }

        .sidebar-nav li a.active {

            /* background: var(--primary); */
            /* color: var(--white); */
            font-weight: 600
        }

        /* === MAIN CONTENT === */
        .main-content h1 {
            font-size: 38px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 20px;
            line-height: 1.3
        }

        .main-content h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 30px 0 12px;
            line-height: 1.3
        }

        .main-content h3 {
            font-size: 17px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 24px 0 10px
        }

        .main-content p {
            font-size: 18px;
            line-height: 1.4;
            color: var(--text-body);
            margin: 0 0 14px
        }

        .main-content ul,
        .main-content ol {
            margin: 0 0 16px 20px;
            font-size: 15px;
            line-height: 1.8;
            color: var(--text-body)
        }

        .main-content a {
            color: var(--primary);
            text-decoration: underline
        }

        .main-content a:hover {
            color: var(--primary-darker)
        }

        /* === NOTE BOX === */
        .note-box {
            background: var(--surface-light);
            border-left: 4px solid var(--primary);
            padding: 16px 18px;
            margin: 20px 0;
            border-radius: 2px
        }

        .note-box strong {
            display: block;
            font-size: 14px;
            margin-bottom: 4px;
            color: var(--text-dark)
        }

        .note-box p {
            margin: 0;
            font-size: 16px;
            color: var(--text-body);
            line-height: 1.6
        }

        .note-box a {
            color: var(--primary)
        }

        /* === VISA LOOKUP SECTION (FOOTER) === */
        /* === VISA LOOKUP FOOTER === */
        .visa-lookup-section {
            background: var(--footer-bg);
            padding: 60px 20px 50px;
            margin-top: auto;
            position: relative;
            overflow: hidden
        }

        .visa-lookup-section::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 200px;
            background: url('image/aramoana-dark.svg') repeat-x right bottom;
            background-size: auto 200px;
            opacity: .4;
            pointer-events: none
        }

        .visa-lookup-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: flex;
            gap: 120px;
            flex-wrap: wrap;
            align-items: flex-start;
            position: relative;
            z-index: 1
        }

        .visa-lookup-box {
            flex: 0 0 520px
        }

        .visa-lookup-box h2 {
            color: var(--white);
            font-size: 42px;
            font-weight: 700;
            margin: 0 0 28px;
            line-height: 1.1
        }

        .visa-lookup-input {
            display: flex;
            gap: 0
        }

        .visa-lookup-input input {
            flex: 1;
            padding: 14px 16px;
            border: 1.5px solid rgba(255,255,255,.45);
            border-radius: 4px;
            font-size: 16px;
            outline: none;
            background: rgba(255,255,255,.06);
            color: var(--white);
            transition: border-color .2s
        }

        .visa-lookup-input input::placeholder {
            color: rgba(255,255,255,.55)
        }

        .visa-lookup-input input:focus {
            border-color: rgba(255,255,255,.85)
        }

        .footer-links {
            flex: 1;
            display: flex;
            gap: 60px;
            flex-wrap: wrap
        }

        .footer-links-col h4 {
            color: var(--white);
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 18px
        }

        .footer-links-col a {
            display: block;
            color: var(--white);
            font-size: 16px;
            text-decoration: underline;
            line-height: 1;
            margin-bottom: 14px;
            opacity: .9;
            transition: opacity .15s
        }

        .footer-links-col a:hover {
            opacity: 1
        }

        /* === FOOTER BRAND STRIP === */
        .footer-brand-strip {
            background: var(--footer-bg);
           
            padding: 28px 20px;
            position: relative;
            overflow: hidden
        }

        .footer-brand-strip::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 180px;
            background: url('image/aramoana-dark.svg') repeat-x right bottom;
            background-size: auto 180px;
            /* opacity: .3; */
            pointer-events: none
        }

        .footer-brand-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            z-index: 1;

            margin-top: 60px;
            margin-bottom: 60px;
        }

        .footer-brand-left {
            display: flex;
            align-items: center;
            gap: 20px
        }

        .footer-mbie {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .footer-mbie-icon {
            flex-shrink: 0
        }

        .footer-mbie-text {
            color: var(--white);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            line-height: 1.5
        }

        .footer-mbie-text small {
            display: block;
            font-weight: 400;
            font-size: 9px;
            letter-spacing: .04em
        }

        .footer-realme-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,.4);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden
        }

        .footer-brand-right {
            text-align: right
        }

        .footer-brand-right .brand-maori {
            color: var(--white);
            font-size: 22px;
            font-weight: 700;
            display: block;
            line-height: 1.2
        }

        .footer-brand-right .brand-en {
            color: rgba(255,255,255,.8);
            font-size: 16px;
            font-weight: 400
        }

        /* === FOOTER BOTTOM === */
        .nz-footer {
            
            padding: 20px 20px;
            position: relative;
            overflow: hidden
        }

        .nz-footer::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 130px;
           
            background-size: auto 130px;
            opacity: .25;
            pointer-events: none
        }

        .nz-footer-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1
        }

        .footer-bottom-links {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center
        }

        .footer-bottom-links a {
            color: var(--white);
            font-size: 14px;
            text-decoration: underline;
            opacity: .9;
            transition: opacity .15s
        }

        .footer-bottom-links a:hover {
            opacity: 1
        }

        .footer-copy {
            color: rgba(255,255,255,.8);
            font-size: 14px
        }

        .user-greeting {
            font-size: 15px;
            color: var(--text-body);
            font-weight: 600
        }

        @media(max-width:800px) {
            .content-wrap {
                flex-direction: column
            }

            .sidebar {
                width: 100%;
                order: 2
            }

            .main-content {
                order: 1
            }

            .nz-nav-inner {
                flex-wrap: wrap
            }

            .visa-lookup-inner {
                flex-direction: column
            }

            .footer-links {
                flex-direction: column;
                gap: 20px
            }

            .nz-header-inner {
                flex-direction: column;
                align-items: flex-start;
                padding: 12px 0
            }
        }

        #Layer_2 {
            color: white;
        }
    </style>
</head>

<body>

    <!-- TOP INFO BAR -->
    <div class="info-bar">
        <div class="info-bar-inner">
            <div class="info-bar-icon">
                <svg id="Layer_2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 34.44 34.44">
                    <g id="Layer_2-2">
                        <path class="cls-1" d="M17.22,34.44c9.5,0,17.22-7.72,17.22-17.22S26.72,0,17.22,0,0,7.72,0,17.22s7.73,17.22,17.22,17.22ZM17.22,1.44c8.7,0,15.78,7.08,15.78,15.78s-7.08,15.78-15.78,15.78S1.44,25.92,1.44,17.22,8.52,1.44,17.22,1.44Z"></path>
                        <path class="cls-1" d="M17.22,12.03c.7,0,1.27-.57,1.27-1.27s-.57-1.27-1.27-1.27-1.27.57-1.27,1.27.57,1.27,1.27,1.27Z"></path>
                        <rect class="cls-1" x="16.27" y="14.1" width="1.89" height="10.85" rx=".72" ry=".72"></rect>
                    </g>
                </svg>
            </div>
            <div class="info-bar-left">
                <h2>Travel disrupted by the Middle East situation</h2>
                <div class="info-date">ISSUED: MONDAY 02 MAR 2026, 07:10 PM NZDT</div>
            </div>
            <div class="info-bar-right">
                We have established a priority phone line for people to contact us about this specific situation. Call 0508 558 855 (toll free from NZ landlines) or +64 9 914 4100 and press option 7. For information about what options are available to you, check:<br>
                <a href="#">Middle East: advice for temporary visa holders in New Zealand affected by travel disruptions</a>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <header class="nz-header">
        <div class="nz-header-inner">
            <a href="<?php echo htmlspecialchars($base . '/index.php', ENT_QUOTES, 'UTF-8'); ?>" class="nz-logo">
                <!-- NZ Fern Logo -->
                <svg class="header__logo-desktop" alt="Immigration New Zealand logo" width="200" height="64" viewBox="0 0 200 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g fill="#1E222C" clip-path="url(#a)">
                        <path d="M11.048 47.597c.128-.157 2.79 10.759 17.607 8.895-2.904-7.11-11.958-11.766-14.055-11.897.398-.314 3.008-2.29 3.43-2.531-.564 1.923 7.939 9.954 14.511 7.73-1.75-5.441-8.97-10.039-10.836-10.11.782-.406 1.564-1.04 2.379-1.433-.43 1.033 5.822 7.253 12.503 6.756.064-3.224-7.169-9.385-8.566-8.895.666-.36 1.686-.968 1.994-1.125.949 2.224 7.117 6.972 11.362 6.194-3.475-7.476-7.9-7.92-7.938-7.927 1.173-.569 1.872-.929 2.084-1.027.012 2.008 6.085 6.534 10.323 5.475-.86-3.33-4.2-6.573-7.476-6.88l2.237-1.08c-.346.628 4.79 4.984 9.65 4.709-2.346-5.795-5.565-5.919-6.72-6.141.655-.301 1.367-.7 2.053-1.066.275.739 2.923 4.565 9.33 3.597-1.617-3.532-4.996-4.71-6.772-4.834.91-.549 1.59-.778 2.25-1.157.456.536 2.052 3.983 8.554 2.727-1.378-3.348-4.36-4.146-5.527-4.375.552-.34 1.2-.7 1.635-.962.436.766 3.745 3.179 7.316 2.172-.686-2.021-3.218-3.231-5.488-3.31a40.698 40.698 0 0 0 1.898-1.327c.327.785 2.27 2.668 6.784 1.406-1.45-2.884-5.156-2.531-5.194-2.512.891-.673 1.148-.909 1.846-1.458.07.033 2.161 3.002 5.816.935-1.103-1.707-2.725-2.616-4.405-2.165.494-.497.917-.857 1.327-1.327-.16.431 2.719 1.975 5.252-.027-1.212-1.196-2.54-1.497-3.982-1.406.288-.425.372-.392.757-.909 0 0 2.263 1.72 4.405-.418-1.745-1.322-3.764-.478-3.77-.478l.826-1.295c.366.49 2.809.962 3.662-.7-.706-.693-2.898-.457-3.13-.294.071-.065.27-.543.398-.798 4.52.563 2.975-4.794 1.956-7.809-17.037 26.384-39.697 7.718-67.41 24.304-1.295 3.427-1.763 7.004-1.423 10.732.038.367.032.654.18 1.485-.052.255.307 2.296 1.044 4.565.545 1.615 1.161 2.701 1.84 4.369.405-.307.866-.66 1.18-.942-1.237-1.792-5.405-12.655-.23-18.993 4.911-.085 5.47 14.212 4.648 15.73.032 0 2.283-1.544 3.155-2.093-1.051-.425-4.77-12.852 1.436-18.012 0 0 5.534 2.158 3.232 15.265.943-.628 2.264-1.197 3.565-1.877-.705-1.25-4.488-10.635 1.565-15.533 3.257 1.36 3.09 11.615 1.814 13.852 1.046-.497 2.168-1.092 3.213-1.576-1.911-.896-1.764-10.543 1.455-13.172 3.585 3.433 2.713 9.202 1.815 11.726 0 0 2.712-1.144 2.693-1.138-2.135-4.1-1.141-9.064 1.141-11.02 1.75 1.145 2.232 4.447 1.43 9.921.763-.313 1.2-.542 1.95-.824-.469-.902-1.7-6.488 1.455-9.496 2.821 1.367 1.821 7.58 1.61 8.215.025-.046 1.654-.654 1.628-.707-.551-1.21-.943-5.808 1.66-8.025 1.033.752 2.123 2.845 1.142 6.809.603-.301 1.257-.563 1.872-.85-.878-1.001-.243-5.037 1.084-6.652 1.59.713 2.27 4.205 1.7 5.363.397-.203.91-.458 1.557-.83-.506-.818-.673-4.579.885-5.606 1.526 1.066 1.372 3.82 1.315 4.435.436-.288 1.712-.955 1.686-.975-.84-.765-1.244-3.29.564-4.813 1.571.353 1.603 3.348 1.469 3.544a49.183 49.183 0 0 0 2.084-1.497c-1.443-.32-1.276-2.682-.494-3.728.955.202 1.628.791 1.943 2.53.372-.346 1.25-1.072 1.52-1.392-.873-.072-1.963-2.002-.616-3.27.404.17 1.648.706 1.77 2.073.179-.13.833-.975.942-1.125-.596-.222-1.27-2.073-.648-2.884.828-.079 1.559 1.06 1.674 1.537l.808-1.197c-.462-.386-1.424-1.498-.577-2.472.93.045 1.263.765 1.462 1.438-9.388 18.738-47.283 21.95-67.057 44.48 0 0-2.059 2.192-2.059 3.238v7.607h.398c1-2.858 4.2-10.131 10.638-15.442M83.1-.003c-2.225.052-3.995 1.949-3.943 4.212.05 2.263 1.91 4.068 4.142 4.009 2.231-.052 4-1.942 3.943-4.212-.058-2.27-1.917-4.068-4.148-4.01h.006Zm3.668 4.022c.044 1.995-1.514 3.67-3.476 3.715-1.968.046-3.603-1.543-3.654-3.532-.052-2.001 1.506-3.669 3.475-3.715 1.962-.045 3.603 1.537 3.655 3.532Z"></path>
                        <path d="M82.69 3.274v-.491h-2.123v.49h.789v2.525h.526V3.274h.808Zm2.95 2.524V2.783h-.546l-.872 1.714-.865-1.714h-.533v3.015h.532V3.83l.629 1.263h.487l.622-1.263V5.8h.545Zm108.414 34.644h-5.098V26.066h5.098c1.564 0 2.847.478 3.738 1.38 1.449 1.485 1.443 3.297 1.43 5.213v1.073c0 1.981.025 3.845-1.43 5.33-.891.903-2.18 1.38-3.738 1.38Zm-2.251-2.603h2.001c.885 0 1.519-.268 1.994-.857.5-.621.577-1.622.577-3.787s-.083-3.047-.577-3.662c-.481-.589-1.109-.857-1.994-.857h-2.001v9.163Zm-56.124 2.603h-9.323v-9.62c0-.962-.013-2.878.436-3.604.487-.766 1.109-1.152 2.327-1.152h6.566v2.603h-5.072c-.353 0-.737.079-1.026.367-.282.287-.378.863-.378 1.242v1.603h5.514v2.61h-5.514v3.335h6.476v2.603l-.006.013ZM91.263 26.066h-9.324v9.628c0 .961-.019 2.877.43 3.597.487.772 1.11 1.15 2.328 1.15h6.572V37.84h-5.072c-.36 0-.744-.079-1.026-.36-.282-.294-.372-.87-.372-1.249v-1.602h5.514v-2.603H84.8v-3.342h6.476v-2.604l-.013-.013Zm69.063 11.773v2.603h-9.188V27.924c.013-.91-.263-1.328-.917-1.727l-.032-.013s-.019-.033-.019-.052c0-.04.025-.066.058-.066h1.673c1.116 0 2.02.354 2.084 3.1v8.673h6.341Zm-81.298 2.603h-2.526l-5.437-8.594v8.594h-2.847V26.066h2.52l5.437 8.581v-8.58h2.853v14.375Zm104.145-7.933v-4.86c-.052-.745-.231-1.144-.815-1.445l-.032-.013s-.019-.033-.019-.053c0-.039.026-.065.058-.065h1.641c1.321-.052 1.988.955 2.013 3.008V40.45h-2.526l-5.437-8.594v8.594h-2.847V26.073h2.526l5.431 8.58v-2.138l.007-.006Zm-14.056-5.036c-.314-.942-.878-1.413-1.686-1.407h-1.045l-5.123 14.376h2.981l.84-2.518h4.873l.827 2.518h2.982l-4.642-12.97h-.007Zm-3.231 7.979 1.68-4.932 1.622 4.932h-3.302Zm-20.859-7.98c-.314-.941-.878-1.412-1.686-1.406h-1.045l-5.13 14.376h2.975l.847-2.518h4.873l.827 2.518h2.975l-4.636-12.97Zm-3.231 7.98 1.686-4.932 1.622 4.932h-3.308Zm-43.525-.576 2.501-8.81h2.135l2.5 8.81 2.136-8.81h2.975l-3.783 14.376h-2.366l-2.527-8.49-2.526 8.49h-2.372l-3.232-12.329c-.308-1.203-.66-1.595-1.456-1.916l-.032-.013s-.025-.033-.025-.052c0-.04.025-.072.057-.072h1.885c1.353.013 2.155.674 2.456 1.988l1.661 6.822.013.006Zm24.994-5.036c.532-.942.898-2.093.93-2.989v-.785h-9.054v2.603h5.655l-5.912 9.451v2.322h9.311v-2.603h-5.938l5.002-7.999h.006ZM129.35 46.224c-.257-.766-.712-1.145-1.372-1.145h-.853l-4.168 11.694h2.424l.686-2.054h3.969l.667 2.054h2.424l-3.777-10.55Zm-2.629 6.494 1.366-4.01 1.321 4.01h-2.687Zm-19.538 3.996c-1.205 0-2.231-.425-3.058-1.269-1.116-1.138-1.116-2.524-1.116-4.44v-.315c0-1.916 0-3.296 1.116-4.44.834-.85 1.834-1.27 3.058-1.27 1.494 0 2.399.387 3.469 1.479l.039.033-1.552 1.582-.032-.039c-.641-.654-1.09-.961-1.917-.961-.552 0-1.039.209-1.366.588-.404.458-.526.93-.526 3.179 0 2.25.129 2.74.526 3.192.321.372.802.575 1.366.575.603 0 1.077-.203 1.462-.628.391-.438.474-1.066.474-1.524v-.353h-1.981v-1.962h4.27v1.688c0 1.74-.295 2.733-1.051 3.538-.834.883-1.898 1.334-3.168 1.334l-.013.013Zm-23.352.052H81.51V50.18l-2.09 4.232H77.86l-2.11-4.238v6.592h-2.314V45.08H75.7l2.944 6.2 2.917-6.2h2.27v11.687Zm12.67 0h-2.315V50.18l-2.09 4.232h-1.565l-2.103-4.238v6.592h-2.32V45.08h2.269l2.937 6.2 2.917-6.2h2.27v11.687Zm54.958-.052c-1.251 0-2.27-.419-3.117-1.288-1.135-1.158-1.135-2.558-1.135-4.5v-.314c0-1.942 0-3.342 1.135-4.5.847-.863 1.866-1.282 3.117-1.282 1.25 0 2.25.419 3.097 1.282 1.147 1.171 1.147 2.603 1.147 4.578v.157c0 1.976 0 3.408-1.147 4.579-.847.863-1.86 1.288-3.097 1.288Zm0-9.764c-.565 0-1.071.215-1.398.601-.411.465-.532.948-.532 3.224 0 2.277.121 2.754.532 3.218.327.386.833.602 1.398.602.564 0 1.051-.216 1.385-.602.416-.47.545-.98.545-3.224 0-2.243-.129-2.753-.545-3.224-.334-.386-.821-.602-1.385-.602v.007Zm-13.793 9.816h-2.321v-9.568h-2.969v-2.119h8.259v2.119h-2.969v9.568Zm28.81 0h-2.059l-4.424-6.985v6.985h-2.315V45.08h2.052l4.424 6.978V45.08h2.322v11.687ZM98.643 48.14v-1.557c.013-.739-.212-1.079-.75-1.4l-.026-.013s-.013-.026-.013-.039c0-.032.02-.052.051-.052h1.36c.91 0 1.654.314 1.699 2.55v9.144h-2.321V48.14Zm-29.508 0v-1.557c.012-.739-.212-1.079-.75-1.4l-.026-.013s-.013-.026-.013-.039c0-.032.02-.052.051-.052h1.36c.916 0 1.654.314 1.699 2.55v9.144h-2.322V48.14Zm73.584 0v-1.557c.013-.739-.212-1.079-.75-1.4l-.026-.013s-.013-.026-.013-.039c0-.032.02-.052.045-.052h1.36c.91 0 1.654.314 1.699 2.55v9.144h-2.321V48.14h.006Zm-20.647 8.626h-2.68l-2.219-4.623h-1.513v4.623h-2.321V45.072h4.501c2.2 0 3.738 1.478 3.738 3.598 0 1.425-.769 2.57-2.051 3.073l2.545 5.023Zm-6.418-6.612h2.039c.936 0 1.564-.595 1.564-1.478s-.628-1.478-1.564-1.478h-2.039v2.956Z"></path>
                    </g>
                    <defs>
                        <clipPath id="a">
                            <path fill="#fff" d="M0 0h200v64H0z"></path>
                        </clipPath>
                    </defs>
                </svg>

            </a>
            <div class="header-actions">
                <?php if ($loggedIn): ?>
                    <span class="user-greeting">Welcome, <?php echo htmlspecialchars(nz_auth_user_name(), ENT_QUOTES, 'UTF-8'); ?></span>
                    <a href="<?php echo htmlspecialchars($dashboardPath, ENT_QUOTES, 'UTF-8'); ?>" class="btn-login">Check Visa</a>
                    <a href="<?php echo htmlspecialchars($logoutPath, ENT_QUOTES, 'UTF-8'); ?>">Logout</a>
                <?php else: ?>
                    <a href="#">Process to apply</a>
                    <a href="#">For employers</a>
                    <a href="<?php echo htmlspecialchars($loginPath, ENT_QUOTES, 'UTF-8'); ?>" class="btn-login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- NAV -->
    <nav class="nz-nav">
        <div class="nz-nav-inner">
            <div class="nz-nav-links">
                <a href="#">Visit <span class="maori">Toro</span> <span class="arrow">&#9662;</span></a>
                <a href="#">Study <span class="maori">Ako</span> <span class="arrow">&#9662;</span></a>
                <a href="#">Work <span class="maori">Mahi</span> <span class="arrow">&#9662;</span></a>
                <a href="#">Live <span class="maori">Ora</span> <span class="arrow">&#9662;</span></a>
                <a href="#">About us <span class="arrow">&#9662;</span></a>
            </div>
            <div class="nz-nav-search">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
            </div>
        </div>
    </nav>

    <!-- BREADCRUMB -->
    <div class="breadcrumb" style="margin-bottom: 100px;">
        <a href="#">Home</a><span class="sep">&rsaquo;</span>
        <a href="#">Process to apply</a><span class="sep">&rsaquo;</span>
        <a href="#">Once you have a visa</a><span class="sep">&rsaquo;</span>
        <a href="#">Manage your visa and passport</a><span class="sep">&rsaquo;</span>
        <span class="current">The Visa Verification Service</span>
    </div>

    <!-- PAGE CONTENT -->
    <div class="content-wrap">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-title">Manage your visa and passport</div>
            <ul class="sidebar-nav">
                <li><a href="#">Using eVisas and visa labels</a></li>
                <li><a href="#">Get a visa sticker for a passport</a></li>
                <li><a href="#">Transferring a visa to a new passport</a></li>
                <li><a href="#">Apply for a transfer or confirmation of a visa</a></li>
                <li><a href="#">Evidence of New Zealand residence</a></li>
                <li><a href="#" class="active">The Visa Verification Service</a></li>
                <li><a href="#">If you cannot get a passport</a></li>
                <li><a href="#">Passports we do not recognise</a></li>
                <li><a href="#">New Zealand citizens travelling on a foreign passport</a></li>
                <li><a href="#">If your passport is lost or stolen</a></li>
                <li><a href="#">Check or change your visa conditions</a></li>
                <li><a href="#">Changes to your circumstances</a></li>
            </ul>
        </aside>

        <!-- MAIN -->
        <div class="main-content">
            <h1>The Visa Verification Service</h1>
            <p>You can check the details of a New Zealand visa using our online Visa Verification Service.</p>

            <h2>About the Visa Verification Service</h2>
            <p>The details of current visa records held by Immigration New Zealand (INZ) can be checked online using the Visa Verification Service. You can view:</p>
            <ul>
                <li>information about a visa</li>
                <li>the personal details of the visa holder, and</li>
                <li>the conditions of the visa &mdash; for example, how long the person can stay in New Zealand, and what they are allowed to do while they are here.</li>
            </ul>

            <h2>Who can use the service</h2>
            <p>Anyone can use the online service to check a New Zealand visa, as long as they have the consent of the visa holder or the person who has the visa, or a lawful purpose.</p>

            <div class="note-box" style="display: flex; flex-direction: column; gap: 14px;">
                <strong>Note</strong>
                <p>The Visa Verification Service is provided through the RealMe system. INZ also offers VisaView services specifically for employers and education providers.</p>
                <a href="#">VisaView for employers</a>
                <a href="#">Using VisaView to check if someone can study at your organization</a>
            </div>

            <h2>Checking a visa</h2>
            <p>When using the service, you must first provide certain basic details about the visa. This gives you access to more information.</p>
            <p>Before a third party can check a visa, they must have the consent of the visa holder, either verbally or in writing. For example, consent can be given in a visa application form or in an employment contract.</p>

            <a href="#">How to use the Visa Verification Service</a>
            <p style="margin-top: 14px;">To use the Visa Verification Service, you will first need to log in using RealMe. If you do not have an account, you will need to set one up.</p>


            <p><strong>If you do not want to share details online
                </strong></p>


            <p>If you do not want to give a third party access to your visa details online, you could instead provide them with copies of: </p>
            <ul>
                <li>your passport, with a current New Zealand visa label or border stamp</li>
                <li>your passport and visa approval notification
                </li>
                <li>your passport with an Australian permanent resident visa and/or a current resident return visa.
                </li>

            </ul>



            <div class="note-box" style="display: flex; flex-direction: column; gap: 14px;">
                <strong>Note</strong>
                <strong>Confirming visa holder identity</strong>
                <p>You cannot confirm a person's identity using the Visa Verification Service, only whether the details of a visa match those in our records. If you are a third-party user, we recommend that you ask to see a passport or other reliable identity document, to confirm the identity of the visa holder.</p>
            </div>


            <h2>How to use the Visa Verification Service
            </h2>

            <p>To use the Visa Verification Service, you will first need to log in using RealMe. If you do not have an account, you will need to set one up.

            </p>

            <p>


                <a href="login.php" id="" style="display:inline-flex;align-items:center;gap:4px;">Login <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg></a>
            </p>
            <p>


                <a href="register.php" id="" style="display:inline-flex;align-items:center;gap:4px;">Create a account <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg></a>
            </p>
            <p>


                <a href="#" id="" style="display:inline-flex;align-items:center;gap:4px;">What is RealMe? <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg></a>
            </p>

            <p>Once you have logged in, enter the following details for the visa you wish to check:

            </p>


            <ul>

                <li> family name </li>
                <li> passport nationality </li>
                <li> current passport number </li>
                <li> date of birth </li>
                <li> gender </li>
                <li> visa start date. </li>
            </ul>

            <p>You can find this information in your current passport, visa approval notification or visa label.

            </p>
            <p style="margin-bottom: 80px;">The details must be entered exactly as they appear in your current visa.

            </p>




            <h2>What you can see</h2>
            <p>Once you have entered the exact visa details of the visa you want to verify, the following information is made available:</p>
            <ul>
                <li>visa type</li>
                <li>visa start date</li>
                <li>number of entries</li>
                <li>Immigration New Zealand (INZ) client number</li>
                <li>family and given names</li>
                <li>gender</li>
                <li>passport number</li>
                <li>first entry before date</li>
                <li>expiry date travel</li>
                <li>visa expiry</li>
                <li>passport nationality</li>
                <li>date of birth</li>
                <li>visa conditions</li>
            </ul>

            <p>The Visa Verification Service does not provide information about:</p>
            <ul>
                <li>travel movements, for example, whether or not someone is in New Zealand</li>
                <li>the status of any current visa applications</li>
                <li>past visas that have already expired.</li>
            </ul>

            <div class="note-box" style="margin-bottom: 80px;">
                <strong>Note</strong>
                <p>If you enter identity details relating to other names/aliases/passports known to INZ the enquiry may still be successful. For example, your maiden name or details of a different passport you have. If you use different identity information from that on your current visa, any identity details provided will be those on your current visa.</p>
            </div>

            <h2>Privacy rules</h2>
            <p>As required by the Privacy Act 2020, all personal information must:</p>
            <ul>
                <li>only be used for lawful purposes</li>
                <li>only retained for as long as it is required</li>
                <li>only shared when authorised by the visa holder or in accordance with a lawful purpose</li>
                <li>be securely destroyed when no longer required (electronic and hard copy)</li>
                <li>be stored or shared in a secure manner.</li>
            </ul>

            <h2 style="margin-top: 80px;">Complaints about the service</h2>
            <p>If you want to make a complaint about the visa verification service, you should contact INZ through the complaints process published on our website.</p>

            <div style="margin-bottom: 80px;"><p>Complaining about INZ services and processes</p>
        
        <a href="#">Complaining about INZ services and processes</a></div>
            <h2>Contact us</h2>
            <p>If you have any further questions about the Visa Verification Service, phone us on:</p>
            <ul>
                <li>+64 9 914 1458 from within the Auckland toll-free calling area</li>
                <li>+64 4 910 9915 from Wellington</li>
                <li>0508 INZ 569 from the rest of New Zealand</li>
            </ul>

            <p>For general visa enquiries telephone <a href="#">0508 967 569</a> .</p>
        </div>
    </div>
<footer class="visa-lookup-section">
    <!-- VISA LOOKUP FOOTER -->
    <div >
        <div class="visa-lookup-inner">
            <div class="visa-lookup-box">
                <h2>Visa lookup</h2>
                <form class="visa-lookup-input" method="GET" action="<?php echo htmlspecialchars($loggedIn ? $dashboardPath : $loginPath, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="text" name="q" placeholder="Visa lookup">
                </form>
            </div>
            <div class="footer-links">
                <div class="footer-links-col">
                    <h4>Find and compare visas</h4>
                    <a href="#">Visit</a>
                    <a href="#">Work</a>
                    <a href="#">Study</a>
                    <a href="#">Live</a>
                    <a href="#">All visas</a>
                </div>
                <div class="footer-links-col">
                    <h4>Popular information</h4>
                    <a href="#">Contact us</a>
                    <a href="#">Residence</a>
                    <a href="#">Checking your application</a>
                    <a href="#">Applying for a visa</a>
                    <a href="#">Documents and proof</a>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER BRAND STRIP -->
    <div>
        <div class="footer-brand-inner">
            <div class="footer-brand-left">
                <div class="footer-mbie"> 
                    <div class="footer-mbie-text">
                        Ministry of Business,<br>Innovation &amp; Employment
                        <small>H&#x12a;kina Whakatutuki</small>
                    </div>
                </div>
                <div class="footer-realme-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44">
                        <circle cx="22" cy="22" r="20" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"/>
                        <path d="M22 8 L34 16 L34 28 L22 36 L10 28 L10 16 Z" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="1.2"/>
                        <circle cx="22" cy="22" r="6" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                    </svg>
                </div>
            </div>
            <div class="footer-brand-right">
                <span class="brand-maori">Te K&#x101;wanatanga o Aotearoa</span>
                <span class="brand-en">New Zealand Government</span>
            </div>
        </div>
    </div>
    <!-- FOOTER BOTTOM -->
    <footer class="nz-footer">
        <div class="nz-footer-inner">
            <div class="footer-bottom-links">
                <a href="#">Glossary</a>
                <a href="#">Accessibility</a>
                <a href="#">Privacy</a>
                <a href="#">Terms of use</a>
                <a href="#">Copyright</a>
                <a href="#">Cookie preferences</a>
            </div>
            <span class="footer-copy">Crown copyright &copy; <?php echo date('Y'); ?></span>
        </div>
    </footer>
</footer>
</body>

</html>