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
my name is omar
</body>

</html>