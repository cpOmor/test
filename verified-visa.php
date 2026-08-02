<?php
require_once __DIR__ . '/nz-auth.php';
define('NZ_VERIFY_INCLUDED', true);
require_once __DIR__ . '/check-status.php';

nz_auth_start_session();

$base = nz_base_path();
$homePath      = $base . '/index.php';
$loginPath     = $base . '/login.php';
$dashboardPath = $base . '/verified-visa.php';
$logoutPath    = $base . '/logout.php';

if (!nz_auth_is_logged_in()) {
    header('Location: ' . $loginPath);
    exit;
}

extract(verify_process_request($dashboardPath));

$userName  = nz_auth_user_name();
$userEmail = nz_auth_user_email();

function nz_v(mixed $value): string
{
    return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Verification Service - New Zealand Immigration</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #2b2b2b;
            background: #f4f7fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column
        }

        /* HEADER */
        .nz-header {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 0 20px
        }

        .nz-header-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 72px;
            gap: 16px;
            flex-wrap: wrap
        }

        .nz-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #1a1a1a
        }

        .nz-logo svg {
            width: 50px;
            height: 38px
        }

        .nz-logo-text {
            font-size: 15px;
            font-weight: 600;
            line-height: 1.3;
            color: #1a1a1a
        }

        .nz-logo-text span {
            display: block;
            font-size: 12px;
            font-weight: 400;
            color: #555
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px
        }

        .user-info {
            font-size: 13px;
            color: #333
        }

        .user-info strong {
            color: #1a1a1a
        }

        .btn-sm {
            display: inline-block;
            padding: 7px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            border: none
        }

        .btn-blue {
            background: #005b99;
            color: #fff !important
        }

        .btn-blue:hover {
            background: #004a80
        }

        .btn-outline {
            background: transparent;
            color: #005b99;
            border: 1px solid #005b99
        }

        .btn-outline:hover {
            background: #f0f6fc
        }

        .btn-red {
            background: #c0392b;
            color: #fff !important;
            text-decoration: none !important
        }

        .btn-red:hover {
            background: #a93226
        }

        /* NAV */
        .nz-nav {
            background: #005b99;
            padding: 0 20px
        }

        .nz-nav-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: flex;
            gap: 0
        }

        .nz-nav a {
            color: #fff;
            text-decoration: none;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 3px solid transparent
        }

        .nz-nav a:hover,
        .nz-nav a.active {
            border-bottom-color: #fff;
            background: rgba(255, 255, 255, .08)
        }

        /* BREADCRUMB */
        .breadcrumb {
            max-width: 1140px;
            margin: 0 auto;
            padding: 14px 20px;
            font-size: 13px;
            color: #555
        }

        .breadcrumb a {
            color: #005b99;
            text-decoration: none
        }

        .breadcrumb a:hover {
            text-decoration: underline
        }

        .breadcrumb sep {
            margin: 0 6px;
            color: #999
        }

        /* PAGE */
        .page-wrap {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 20px 60px;
            flex: 1;
            width: 100%
        }

        .page-title {
            font-size: 26px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 6px
        }

        .page-subtitle {
            font-size: 14px;
            color: #555;
            margin: 0 0 28px
        }

        /* SEARCH CARD */
        .search-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
            padding: 28px 28px;
            border: 1px solid #e2e6ea;
            margin-bottom: 28px
        }

        .search-card h2 {
            font-size: 18px;
            font-weight: 700;
            color: #1a3a5c;
            margin: 0 0 6px
        }

        .search-card .note {
            font-size: 14px;
            color: #555;
            margin: 0 0 18px;
            line-height: 1.6
        }

        .search-form {
            display: flex;
            gap: 10px;
            align-items: stretch
        }

        .search-form input {
            flex: 1;
            padding: 12px 14px;
            border: 1px solid #c5cdd8;
            border-radius: 5px;
            font-size: 14px;
            outline: none;
            transition: border-color .2s
        }

        .search-form input:focus {
            border-color: #005b99;
            box-shadow: 0 0 0 3px rgba(0, 91, 153, .12)
        }

        .search-form button {
            padding: 12px 24px;
            background: #005b99;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap
        }

        .search-form button:hover {
            background: #004a80
        }

        /* ALERT */
        .alert {
            padding: 12px 16px;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 20px
        }

        .alert-error {
            background: #fdecea;
            border: 1px solid #f4c7c3;
            color: #b42318
        }

        /* RESULT CARD */
        .result-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
            border: 1px solid #e2e6ea;
            overflow: hidden
        }

        .result-banner {
            text-align: center;
            padding: 28px 20px 16px;
            border-bottom: 1px solid #e2e6ea;
            background: linear-gradient(180deg, #f0f8f0 0%, #fff 100%)
        }

        .result-banner .icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #00843d;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 6px 16px rgba(0, 132, 61, .25)
        }

        .result-banner h2 {
            font-size: 22px;
            font-weight: 700;
            color: #00843d;
            margin: 0
        }

        .result-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 22px;
            border-bottom: 1px solid #e2e6ea;
            background: #fafbfc;
            flex-wrap: wrap;
            gap: 10px
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em
        }

        .status-approved {
            background: #e6f4ea;
            color: #1e7e34;
            border: 1px solid #b7e1c8
        }

        .status-pending {
            background: #f5f5f5;
            color: #555;
            border: 1px solid #ddd
        }

        .status-rejected {
            background: #fdecea;
            color: #b42318;
            border: 1px solid #f4c7c3
        }

        /* DETAILS */
        .result-body {
            display: flex;
            flex-wrap: wrap
        }

        .result-photo {
            width: 200px;
            padding: 22px;
            border-right: 1px solid #e2e6ea;
            background: #fafbfc;
            display: flex;
            align-items: flex-start;
            justify-content: center
        }

        .photo-frame {
            width: 100%;
            aspect-ratio: 3/4;
            border-radius: 8px;
            border: 1px solid #ddd;
            overflow: hidden;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .photo-frame .no-photo {
            font-size: 13px;
            color: #888;
            text-align: center;
            padding: 12px
        }

        .result-details {
            flex: 1;
            padding: 22px 24px;
            min-width: 0
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 20px
        }

        .detail-item {
            border-bottom: 1px dashed #e8eaed;
            padding: 8px 0 10px
        }

        .detail-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #7a8599;
            letter-spacing: .04em;
            margin-bottom: 3px
        }

        .detail-value {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            overflow-wrap: anywhere;
            line-height: 1.4
        }

        /* QR */
        .result-qr {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #e2e6ea;
            background: #fafbfc
        }

        .result-qr img {
            width: 120px;
            height: 120px;
            /* border: 1px solid #ddd; */
            /* border-radius: 8px; */
            padding: 4px;
            background: #fff
        }

        .result-qr p {
            margin: 8px 0 0;
            font-size: 12px;
            color: #888
        }

        /* BACK LINK */
        .back-link {
            display: inline-block;
            margin-top: 20px;
            font-size: 14px;
            color: #005b99;
            text-decoration: none;
            font-weight: 600
        }

        .back-link:hover {
            text-decoration: underline
        }

        /* A4 VISA PAGES */
        .visa-page {
            width: 794px;
            background: #fff;
            margin: 14px auto;
            padding: 28px 44px 32px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06)
        }

        .visa-page p {
            margin-bottom: 6px
        }

        .visa-page-3 {
            background-image: url('image/nz-watermark.png');
            background-repeat: repeat;
            background-size: 50% auto
        }

        .evcard {
            border: 2px solid #003166;
            margin-bottom: 10px
        }

        .evcard-body {
            padding: 10px 12px
        }

        .screen-actions {
            max-width: 794px;
            margin: 14px auto 0;
            display: flex;
            gap: 10px
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
            color: #fff
        }

        .action-back {
            background: #334155
        }

        .action-back:hover {
            background: #1e293b
        }

        .action-print {
            background: #2563eb
        }

        .action-print:hover {
            background: #1d4ed8
        }

        /* FOOTER */
        .nz-footer {
            background: #082a3e;
            padding: 20px;
            text-align: center;
            margin-top: auto
        }

        .nz-footer span {
            color: #7aa8cc;
            font-size: 12px
        }

        @media(max-width:700px) {
            .search-form {
                flex-direction: column
            }

            .nz-header-inner {
                flex-direction: column;
                align-items: flex-start;
                padding: 12px 0
            }

            .visa-page {
                width: 100%;
                padding: 18px 16px 24px
            }

            .screen-actions {
                flex-direction: column
            }
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important
            }

            html,
            body {
                width: 100%;
                height: 100%;
                background: #fff;
                margin: 0;
                padding: 0
            }

            .nz-header,
            .nz-nav,
            .breadcrumb,
            .search-card,
            .nz-footer,
            .header-right,
            .screen-actions,
            .result-banner {
                display: none !important
            }

            .page-wrap {
                margin: 0;
                padding: 0;
                max-width: none;
                width: 100%
            }

            body {
                background: #fff
            }

            .visa-page {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                padding: 12mm 16mm 12mm;
                box-shadow: none !important;
                page-break-after: always;
                break-after: page
            }

            .visa-page:last-of-type {
                page-break-after: auto;
                break-after: auto
            }

            @page {
                size: A4 portrait;
                margin: 0
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header class="nz-header">
        <div class="nz-header-inner">
            <a href="<?php echo htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8'); ?>" class="nz-logo">
                <svg viewBox="0 0 60 45" xmlns="http://www.w3.org/2000/svg">
                    <rect width="60" height="45" fill="#00247d" />
                    <g fill="#cc142b">
                        <polygon points="37,11 38.5,16 43.5,16 39.5,19 41,24 37,21 33,24 34.5,19 30.5,16 35.5,16" />
                        <polygon points="47,18 48,21 51,21 48.5,23 49.5,26 47,24 44.5,26 45.5,23 43,21 46,21" />
                        <polygon points="47,6 48,9 51,9 48.5,11 49.5,14 47,12 44.5,14 45.5,11 43,9 46,9" />
                        <polygon points="43,30 44,33 47,33 44.5,35 45.5,38 43,36 40.5,38 41.5,35 39,33 42,33" />
                    </g>
                </svg>
                <div class="nz-logo-text">
                    NEW ZEALAND GOVERNMENT
                    <span>Immigration New Zealand</span>
                </div>
            </a>
            <div class="header-right">
                <span class="user-info">Logged in as <strong><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></strong></span>
                <a href="<?php echo htmlspecialchars($logoutPath, ENT_QUOTES, 'UTF-8'); ?>" class="btn-sm btn-red">Logout</a>
            </div>
        </div>
    </header>

    <!-- NAV -->
    <nav class="nz-nav">
        <div class="nz-nav-inner">
            <a href="<?php echo htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8'); ?>">Home</a>
            <a href="<?php echo htmlspecialchars($dashboardPath, ENT_QUOTES, 'UTF-8'); ?>" class="active">Visa Verification</a>
        </div>
    </nav>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="<?php echo htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8'); ?>">Home</a>
        <sep>&rsaquo;</sep>
        <a href="<?php echo htmlspecialchars($dashboardPath, ENT_QUOTES, 'UTF-8'); ?>">Visa Verification Service</a>
        <sep>&rsaquo;</sep>
        Check a visa
    </div>

    <!-- PAGE -->
    <div class="page-wrap">

        <?php if ($errorMessage !== ''): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!$verified || !is_array($record)): ?>
            <!-- SEARCH -->
            <section class="search-card">
                <h2>Check a visa</h2>
                <p class="note">Enter the Passport Number, Visa Number, or Reference Number to retrieve official verification details. The details must be entered exactly as they appear in the current visa.</p>
                <form method="POST" action="<?php echo htmlspecialchars($dashboardPath, ENT_QUOTES, 'UTF-8'); ?>" class="search-form">
                    <input type="text" name="visa_number"
                        value="<?php echo htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Enter Passport Number, Visa Number, or Reference Number" required>
                    <button type="submit">Verify</button>
                </form>
            </section>
        <?php else: ?>
            <!-- VISA VERIFIED BANNER -->
            <div class="result-banner" style="border-radius:8px;border:1px solid #e2e6ea;margin-bottom:14px">
                <div class="icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 7 10 17l-6-6" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <h2>Visa Verified</h2>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($verified && is_array($record)): ?>
        <?php
        $cond = trim((string) ($remarks['employmentConditions'] ?? ''));
        $minWage = trim((string) ($remarks['minimumHourlyWage'] ?? ''));
        $verUrl = trim((string) ($remarks['verificationWebsite'] ?? 'https://immigration.govt.nz'));

        $qrStatus = trim((string) ($record['status'] ?? 'Issued'));
        $qrName = trim((string) ($record['full_name'] ?? ''));
        $qrPassportNumber = trim((string) ($record['passport_number'] ?? ''));
        $qrNationality = trim((string) ($remarks['passportNationality'] ?? ($record['nationality'] ?? '')));
        $qrReferenceNumber = trim((string) ($remarks['nzetaReferenceNumber'] ?? ($record['visa_number'] ?? '')));
        $qrIssueDate = trim((string) ($remarks['nzetaIssueDate'] ?? ($record['issue_date'] ?? '')));
        $qrExpiryDate = trim((string) ($remarks['nzetaExpiryDate'] ?? ($record['evisa_expire_date'] ?? '')));

        $qrPayloadText = "Status: " . $qrStatus . "\n"
            . "Name: " . $qrName . "\n"
            . "Passport Number: " . $qrPassportNumber . "\n"
            . "Passport Nationality: " . $qrNationality . "\n"
            . "NZeTA Reference Number: " . $qrReferenceNumber . "\n"
            . "NZeTA issue Date: " . $qrIssueDate . "\n"
            . "NZeTA expiry date: " . $qrExpiryDate;

        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=0&data=' . rawurlencode($qrPayloadText);
        ?>

        <!-- ACTIONS -->
        <div class="screen-actions">
            <a class="action-btn action-back" href="<?php echo nz_v($dashboardPath); ?>">&larr; Check Another Visa</a>
            <button class="action-btn action-print" onclick="window.print()">Print / Save PDF</button>
        </div>

        <!-- ═══════════════════════════════════════════════════════
     PAGE 1 — NZeTA LETTER
     ═══════════════════════════════════════════════════════ -->
        <div class="visa-page">
            <div>
                <div>
                    <div style="font-weight: bold;">NZeTA (New Zealand Electronic Travel Authority)
                        <strong><?php echo nz_v($record['visa_number']); ?></strong> has been issued
                    </div>
                    <img src="image/nz-header.png" alt="New Zealand Immigration and NZeTA"
                        style="display:block; height:auto; margin:12px 0 12px 0;">
                </div>

                <div style="font-size:12px; margin-bottom:18px;">
                    Status: <?php echo nz_v($record['status'] ?? 'Issued'); ?><br>
                    Name: <?php echo nz_v($record['full_name']); ?><br>
                    Passport Number: <?php echo nz_v($record['passport_number']); ?><br>
                    Passport Nationality: <?php echo nz_v($remarks['passportNationality'] ?? $record['nationality']); ?><br>
                    NZeTA Reference Number: <?php echo nz_v($remarks['nzetaReferenceNumber'] ?? ''); ?><br>
                    NZeTA expiry date: <?php echo nz_v($remarks['nzetaExpiryDate'] ?? ''); ?>
                </div>
            </div>

            <p>Your NZeTA (New Zealand Electronic Travel Authority) has been <strong>issued</strong>.</p>
            <p>Your NzeTA let's you <strong>travel to or transit</strong> Newzealand.</p>
            <p>When you travel to New Zealand, you must have a valid Australian permanent resident or Resident Return
            </p>
            <p>Visa. This will be checked when you board your plane or cruise. If you don't have this, you will be denied
                boarding and need to request a new NZeTA.
            </p>
            <p style="margin-bottom: 40px;">You must also check in with the passport listed in this email.
            </p>
            <p style="font-size: 10px; font-weight: bold; margin-bottom: 40px;">Now your NZeTA has been issued, what next?
            </p>

            <ul style="margin-left: 38px;">
                <li>
                    Check that the details in this notification (including your given, middle and family names) exactly
                    match your passport details. If they do not match, or your details change, you will not be allowed to
                    travel to New Zealand using this NZeTA. You can use our online form to correct some details, otherwise
                    you must request a new NZeTA.
                </li>
                <div style="margin-top: 20px;"></div>
                <li>
                    Your NZeTA is not a visa and does not guarantee entry into New Zealand. If you are flying to New Zealand
                    and intend to enter, you must apply for entry permission by completing an arrival card and presenting it
                    to
                    an immigration officer when you arrive in New Zealand. You must also answer some questions about
                    biosecurity and customs
                </li>
            </ul>
            <div style="margin-top: 40px;"></div>

            <p style="font-size: 12px; margin-bottom: 12px;">For information on what to see and do in</p>
            <p style="font-size: 12px; margin-bottom: 12px;">New Zealand, visit <span style="color: blue; cursor: pointer;">www.newzeaIand.com.</span></p>
            <p style="font-size: 12px; margin-bottom: 12px;">See you in New Zealand</p>
            <p style="font-size: 12px;margin-bottom: 12px; ">This message and any files transmitted with it are confidential and solely for the use of the intended recipient. If you are not the intended recipient or the person
                responsible for delivery to the intended recipient, be advised that you have received this message in error and that any use is strictly.</p>
        </div>

        <!-- ═══════════════════════════════════════════════════════
     PAGE 2 — WORK VISA APPROVAL
     ═══════════════════════════════════════════════════════ -->
        <div class="visa-page" style="margin-bottom: 10px;">
            <div style="margin-bottom: 10px; display: flex; justify-items: center; justify-content: space-between;">
                <div style="display: flex; flex-direction: column; justify-items: end; ">
                    <p style=" margin-top: 30px;">Date : <?php echo date('j F Y'); ?></p>
                    <p> New Zealand Work Visa Approval</p>
                </div>
                <div style="width: 150px;">
                    <img src="image/nz-logo.jpg" alt="New Zealand Immigration and NZeTA"
                        style="display:block; width: 150px; height:auto; margin:12px 0 12px 0;">
                </div>
            </div>

            <p style="margin-bottom: 14px;">
                Application Reference Number <strong><?php echo nz_v($record['visa_number']); ?></strong> a New Zealand Work Visa- Essential Skills has been approved. If this
                application has been made through an immigration adviser, lawyer or other representative who is exempt from
                licensing, this entire document must be provided to the applicant.
            </p>

            <div class="evcard">
                <div class="evcard-body">
                    <p style="text-align:center; font-size: 16px; margin-bottom: 10px;"><strong>Work Visa Details</strong></p>
                    <div>
                        <table style="width:100%; font-size:13px; margin-bottom:18px;">
                            <tr>
                                <td style="font-weight:bold; width: 100px;">Applicant</td>
                                <td>: <?php echo nz_v($record['full_name']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold;">Date of Birth</td>
                                <td>: <?php echo nz_v($record['birthday'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold;">Gender</td>
                                <td>: <?php echo nz_v($record['gender'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold;">Nationality</td>
                                <td>: <?php echo nz_v($record['nationality']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold;">Passport No</td>
                                <td>: <?php echo nz_v($record['passport_number']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold;">Client Number</td>
                                <td>: <?php echo nz_v($remarks['clientNumber'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top:10px; font-size:14px;">
                                    The Start Date of your Visa is: <strong><?php echo nz_v($record['issue_date']); ?></strong><br>
                                    You must arrive in New Zealand before: <strong><?php echo nz_v($remarks['lastArrivalDate'] ?? $record['evisa_expire_date']); ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top:10px; font-size:12px;">
                                    The number of time you may enter New Zealand using this visa is : <strong><?php echo nz_v($remarks['entryType'] ?? 'Multiple'); ?></strong><br>
                                    Your Visa expires and you must leave New Zealand on or before: <strong><?php echo nz_v($record['evisa_expire_date']); ?></strong> <br>
                                    Your Visa expires and you must leave New Zealand on or before: <strong><?php echo nz_v($record['evisa_expire_date']); ?></strong>
                                </td>
                            </tr>
                        </table>
                        <div style="margin-bottom: 10px;">
                            Your Employment has been assessed as Mid-Skilled.
                        </div>
                        <div style="margin-bottom: 10px;">
                            The Conditions of your visa: Stay subject to grant of entry permission. You must leave before visa expiry or face
                            deportation. Financial support evidence not required. Return/onward ticket not required. The holder May only
                            work as factory worker/ packing support staff at DB breweries- Christchurch, New Zealand. You must provide
                            evidence of remuneration payment if requested by an immigration officer.
                        </div>
                        <div>
                            You must be paid at least NZ$ 23 per hour.
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top:30px;">
                <div style="font-weight:bold; text-align:center; font-size:17px; margin-bottom:16px;">
                    PRINT THIS DOCUMENT AND CARRY IT WITH YOUR PASSPORT AT ALL TIMES
                </div>
                <div style="font-size:13px; margin-bottom:10px;">
                    The details above reflect the electronic record of your visa held by immigration New Zealand (INZ). You do not require a visa label in your passport. Do not attempt to alter this letter. It is an offence under the immigration Act 2009 to use a document that you know has been altered.
                </div>
                <div style="font-size:13px; margin-bottom:10px;">
                    Please check that the above visa details match your passport before you travel and contact INZ immediately if there are any errors. If you are offshore, you may only enter New Zealand after the start date of your Visa.
                </div>
                <div style="font-size:13px; margin-bottom:10px;">
                    You may be asked to show this letter when you check in for your flight to New Zealand and/or when you arrive at the New Zealand border. IF you cannot show this letter when asked, the airline may not let you board your flight or you may be delayed when entering New Zealand.
                </div>
                <div style="font-size:13px; margin-bottom:10px;">
                    You can only hold one visa at a time. Any previous visa you held is now void and has been replaced by the visa referred to in this letter.
                </div>
                <div style="font-size:13px; margin-bottom:10px; font-weight:bold;">
                    How can you prove your visa details without a visa label in your passport?
                </div>
                <div style="font-size:13px; margin-bottom:10px;">
                    Your employer can verify the details of your visa online using Visa View. With your consent, other people or organizations such as health care providers or travel agents can verify the details of your visa using the visa verification service.
                </div>
                <div style="font-size:13px; margin-bottom:18px;">
                    Verification service &gt;&gt; <a href="https://nzta.immigration.govt.nz/check-status" style="color:#2563eb; text-decoration:underline;">https://nzta.immigration.govt.nz/check-status</a>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:30px;">
                    <img src="image/nz-ministry-logo.jpg" alt="Ministry of Business, Innovation &amp; Employment" style="width:100%;">
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════
     PAGE 3 — e-Visa for New Zealand
     ═══════════════════════════════════════════════════════ -->
        <div class="visa-page" style="margin-bottom: 10px;">
            <div class="visa-page-3">
                <div style="text-align:center; font-size:28px; font-weight:700; margin-top:18px; margin-bottom:8px; letter-spacing:1px; z-index:1; position:relative;">e-Visa for New Zealand</div>
                <hr style="border:1.5px solid #111; margin-bottom:28px; z-index:1; position:relative;">
                <div style="font-size:13.55px; font-weight:700; margin-bottom:10px; z-index:1; position:relative;">
                    You are required to bring this paper e-Visa with you as the airline requires you to produce it for verification when you check -in.
                </div>
                <table style="width:100%; font-size:14px; font-weight:500; margin-bottom:18px; z-index:1; position:relative;">
                    <tr>
                        <td style="width:170px; font-weight:700;">e-Visa Number:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($record['visa_number']); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Name :</td>
                        <td style="font-weight:700; text-transform:uppercase;"> <?php echo nz_v($record['full_name']); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Date of birth:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($record['birthday'] ?? ''); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Sex:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($record['gender'] ?? ''); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Nationality:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($record['nationality']); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Travel documents number:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($record['passport_number']); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Visa issue date:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($record['issue_date']); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Visa valid till:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($record['evisa_expire_date']); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Type of visa:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($record['visa_type']); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Employer:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($remarks['employerName'] ?? ''); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Designation:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($remarks['jobTitle'] ?? ''); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Wages:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($remarks['monthlyWage'] ?? ''); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Visa issuing authority:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($remarks['visaIssuingAuthority'] ?? ''); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Visa processing fee:</td>
                        <td style="font-weight:700;"> <?php echo nz_v($record['visa_fee'] ?? ''); ?> </td>
                    </tr>
                </table>
                <div style="margin: 30px 0 18px 18px; z-index:1; position:relative;">
                    <img src="https://barcode.tec-it.com/barcode.ashx?data=<?php echo urlencode(nz_v($record['visa_number'])); ?>&code=Code128&translate-esc=false" alt="Barcode" style="height:60px; display:block;">
                </div>
                <div style="font-size:14px; font-weight:700; margin-bottom:24px; z-index:1; position:relative;">
                    You are required to bring this paper e-Visa with as the airline requires you to produce it for verification when you check in.
                </div>
                <div style="font-size:14px; font-weight:500; margin-bottom:24px; z-index:1; position:relative;">
                    You may wish to visit our website at <a href="https://immigration.govt.nz" style="color:#2563eb; text-decoration:underline;">https://immigration.govt.nz</a> using save to verify the information contained in this e-Visa .
                </div>
                <div style="font-size:14px; margin-bottom:24px; z-index:1; position:relative;"><span style="font-weight:700; text-decoration:underline;">Important Note:</span> This e-Visa is he should to you based on the information provided in the application for which you have truthfully declared to be so or for which you had consented for a proxy to submit on your behalf and are fully aware of the information so provided by your Authorized proxy. A New Zealand visa in not an immigration pass. it is a pre-entry
                </div>
                <div style="font-size:14px; margin-bottom:24px; z-index:1; position:relative;">
                    permission for you to travel to, and seek entry: into New Zealand. A holder of a valid New Zealand visa who is found suitable for entry into New Zealand will be issued with an immigration pass to enter and remain in New Zealand. Possession of a valid visa alone does not guarantee entry into New Zealand. You must also meet the following entry requirements:
                </div>
                <div style="font-size:14px; margin-bottom:24px; z-index:1; position:relative;">
                    <ol style="margin-left: 30px; list-style-type: lower-roman;">
                        <li style="margin-bottom:2px;">Hold a passport with at least 6 months validity.</li>
                        <li style="margin-bottom:2px;">Have sufficient support for the period of stay in New Zealand.</li>
                        <li style="margin-bottom:2px;">Have confirmed onward/return air ticket(s).</li>
                    </ol>
                </div>
                <div style="font-size:14px; margin-bottom:10px; z-index:1; position:relative;">
                    The Grant of immigration pass to you will be determined by the immigration and checkpoints authority (ICA) officers at the point of entry. The period of stay granted is shown on the Visa pass endorsement given on your passport and it is not tired to validity of this visa. Please check your passport for the arrival endorsement and take note of the period of stay granted before leaving the checkpoint.
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════
     PAGE 4 — VISA GRANT LETTER
     ═══════════════════════════════════════════════════════ -->
        <div class="visa-page" style="position:relative; min-height:1100px; font-family: Arial, Helvetica, sans-serif;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <img src="image/nz-fidn-logo.jpg" alt="New Zealand FIDN" style="height:auto; width: 105%; margin-left: -2.5%;">
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="flex:1;">
                    <div style="text-align:center; font-size:18px; font-weight:700; text-decoration:underline; margin-bottom:18px;">VISA GRANT LETTER</div>
                </div>
                <div style="width:200px; text-align:right; position: absolute; right: 50px;">
                    <img src="<?php echo nz_v($qrImageUrl); ?>" alt="QR Code" style="width:200px; height:200px; ">
                </div>
            </div>
            <div style="font-size:16px; margin-bottom:40px; margin-top:10px;">Dear Mr, <span style="font-weight:400;"> <?php echo nz_v($record['full_name']); ?> </span></div>
            <div style="font-size:16px; margin-bottom:10px;">Grant Letter of Electronic Travel Authorization for New Zealand</div>
            <div style="font-size:16px; margin-bottom:10px;">Status: <span style="font-weight:500;">Issued</span></div>
            <div style="font-size:16px; margin-bottom:10px;">Name : <span style="font-weight:500; text-transform:uppercase;"> <?php echo nz_v($record['full_name']); ?> </span></div>
            <div style="font-size:16px; margin-bottom:10px;">Passport Number : <span style="font-weight:500;"> <?php echo nz_v($record['passport_number']); ?> </span></div>
            <div style="font-size:16px; margin-bottom:10px;">Visa Number : <span style="font-weight:500;"> <?php echo nz_v($record['visa_number']); ?> </span></div>
            <div style="font-size:16px; margin-bottom:10px;">Visa issue Date: <span style="font-weight:500;"> <?php echo nz_v($record['issue_date']); ?> </span></div>
            <div style="font-size:16px; margin-bottom:10px;">Visa Valid Till : <span style="font-weight:500;"> <?php echo nz_v($record['evisa_expire_date']); ?> </span></div>
            <div style="font-size:16px; margin-bottom:18px;">You are now authorised to pass through all international airports in New Zealand as listed
                international passenger. When you travel to New Zealand, you will need to bring the
                passport you used to apply for visa, as the visa is electronically linked to it.</div>
            <div style="font-size:16px; margin-bottom:18px;">New Zealand border services agency and Airline check staff will have electronic
                access to find out your visa online status. You can check your online status Go to
                https://nzeta.immigration.govt.nz/check-status get your visa status,</div>
            <div style="font-size:16px; margin-bottom:18px;">Any opinions expressed in the message are not necessarily those of the ministry of
                Business, Innovation and Employment. This message and any transmitted file with it
                are confidential and solely for the use of the intended recipient. You are not intended
                recipient or the person responsible for delivery to the intended recipient, be advised
                that you have received this message in error and that any use is strictly prohibited.
                Please contact the sender and delete the message and any attachments from your
                computer.
            </div>
            <div style="font-size:16px; margin-bottom:30px;">
                <p style="margin-bottom: 30px;">Thank you</p><br>Immigration New Zealand
            </div>
            <div style="font-size:16px; margin-top: 18px; font-weight:700; text-decoration: underline;">
                <span style="color:#000;">*Note: this paper Authorized by immigration New Zealand and valid till the end of visa expire date</span>
            </div>
            <div style="display: flex; justify-content: flex-end; margin-top: 18px;">
                <img src="image/nz-immigration-logo.jpg" alt="Immigration New Zealand" style="width: 100px; height: auto;">
            </div>
        </div>

        <div class="screen-actions" style="margin-bottom:30px;">
            <a class="action-btn action-back" href="<?php echo nz_v($dashboardPath); ?>">&larr; Check Another Visa</a>
        </div>
    <?php endif; ?>

    <!-- FOOTER -->
    <footer class="nz-footer">
        <span>Te Kāwanatanga o Aotearoa &mdash; New Zealand Government &middot; Crown copyright &copy; <?php echo date('Y'); ?></span>
    </footer>

    <?php if ($verified && is_array($record)): ?>
        <script>
            var debugData = <?php echo json_encode($debugPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            console.log('all_data:', debugData);
        </script>
    <?php endif; ?>

</body>

</html>