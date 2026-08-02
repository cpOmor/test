<?php
require_once __DIR__ . '/api/config.php';

function verify_table_safe(string $table): string
{
    $table = trim($table);
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
        throw new RuntimeException('Invalid table name');
    }
    return $table;
}

function verify_db(): PDO
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
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function verify_find_by_keyword(string $keyword): ?array
{
    $keyword = strtoupper(trim($keyword));
    if ($keyword === '') {
        return null;
    }

    $table = verify_table_safe(nz_mysql_table());
    $pdo = verify_db();

    $sql = "SELECT *
            FROM `{$table}`
            WHERE destination_country = :country
              AND (UPPER(passport_number) = :keyword OR UPPER(ref_number) = :keyword OR UPPER(visa_number) = :keyword)
            ORDER BY id DESC
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':country' => 'New Zealand',
        ':keyword' => $keyword,
    ]);

    $row = $stmt->fetch();
    return is_array($row) ? $row : null;
}

function verify_find_by_id(int $id): ?array
{
    if ($id <= 0) {
        return null;
    }

    $table = verify_table_safe(nz_mysql_table());
    $pdo = verify_db();

    $sql = "SELECT *
            FROM `{$table}`
            WHERE id = :id AND destination_country = :country
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':country' => 'New Zealand',
    ]);

    $row = $stmt->fetch();
    return is_array($row) ? $row : null;
}

function verify_format_date(?string $value): string
{
    $value = trim((string) $value);
    if ($value === '') {
        return '-';
    }
    $time = strtotime($value);
    if ($time === false) {
        return $value;
    }
    return date('d-M-Y', $time);
}

function verify_format_date_short(?string $value): string
{
    $value = trim((string) $value);
    if ($value === '') {
        return '-';
    }
    $time = strtotime($value);
    if ($time === false) {
        return $value;
    }
    return date('d-m-Y', $time);
}

function verify_applicant_image_url(string $filename): string
{
    $filename = trim($filename);
    if ($filename === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $filename) === 1) {
        return $filename;
    }
    if (str_starts_with($filename, '/')) {
        return $filename;
    }
    $safe = basename($filename);
    if ($safe === '') {
        return '';
    }
    $encoded = rawurlencode($safe);

    $urlCandidates = [
        '/api/uploads/applicants/' . $encoded,
        '/evisa/e-visa/api/uploads/applicants/' . $encoded,
        '/e-visa/api/uploads/applicants/' . $encoded,
        '/evisa/zn-verification/api/uploads/applicants/' . $encoded,
    ];

    $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    foreach ($urlCandidates as $url) {
        $url = '/' . ltrim($url, '/');
        if ($docRoot !== '' && is_file($docRoot . $url)) {
            return $url;
        }
    }

    $fileCandidates = [
        __DIR__ . '/api/uploads/applicants/' . $safe,
        dirname(__DIR__) . '/e-visa/api/uploads/applicants/' . $safe,
        dirname(__DIR__, 2) . '/public_html/api/uploads/applicants/' . $safe,
    ];

    foreach ($fileCandidates as $filePath) {
        if (!is_file($filePath) || !is_readable($filePath)) {
            continue;
        }
        $binary = file_get_contents($filePath);
        if ($binary === false) {
            continue;
        }
        $mime = 'image/jpeg';
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $mime = 'image/png';
        } elseif ($ext === 'webp') {
            $mime = 'image/webp';
        }
        return 'data:' . $mime . ';base64,' . base64_encode($binary);
    }
    return '';
}

function verify_first_non_empty(array $values): string
{
    foreach ($values as $value) {
        $text = trim((string) $value);
        if ($text !== '') {
            return $text;
        }
    }
    return '';
}

function verify_record_or_remarks(array $record, array $remarks, array $recordKeys, array $remarksKeys): string
{
    $recordValues = [];
    foreach ($recordKeys as $key) {
        $recordValues[] = $record[$key] ?? '';
    }
    $remarksValues = [];
    foreach ($remarksKeys as $key) {
        $remarksValues[] = $remarks[$key] ?? '';
    }
    return verify_first_non_empty(array_merge($recordValues, $remarksValues));
}

function verify_status_meta(string $status): array
{
    $raw = trim($status);
    $value = strtolower($raw);
    if (in_array($value, ['approved', 'active', 'verified', 'success', 'issued'], true)) {
        return ['label' => 'Approved', 'class' => 'status-approved'];
    }
    if (in_array($value, ['pending', 'processing', 'in-review', 'in review'], true)) {
        return ['label' => 'Pending', 'class' => 'status-pending'];
    }
    if (in_array($value, ['rejected', 'declined', 'denied', 'invalid'], true)) {
        return ['label' => 'Rejected', 'class' => 'status-rejected'];
    }
    if ($raw === '') {
        return ['label' => '-', 'class' => 'status-pending'];
    }
    return ['label' => ucfirst($raw), 'class' => 'status-pending'];
}
        
function verify_absolute_current_url(array $query): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $path = trim((string) ($_SERVER['PHP_SELF'] ?? ''));
    $qs = http_build_query($query);
    if ($host === '') {
        return $path . ($qs !== '' ? ('?' . $qs) : '');
    }
    return $scheme . '://' . $host . $path . ($qs !== '' ? ('?' . $qs) : '');
}

function verify_process_request(?string $overrideSelfPath = null): array
{
$searchValue = '';
$errorMessage = '';
$record = null;

$verifySelfPath = $overrideSelfPath ?? ($_SERVER['PHP_SELF'] ?? 'check-status.php');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $searchValue = trim((string) ($_POST['visa_number'] ?? ''));
    if ($searchValue === '') {
        header('Location: ' . $verifySelfPath . '?err=empty');
        exit;
    }
    try {
        $found = verify_find_by_keyword($searchValue);
        if (is_array($found)) {
            header('Location: ' . $verifySelfPath . '?verified=1&id=' . (int) $found['id']);
            exit;
        }
        header('Location: ' . $verifySelfPath . '?err=not_found&q=' . urlencode($searchValue));
        exit;
    } catch (Throwable $error) {
        error_log('[zn-verification] check-status error: ' . $error->getMessage());
        header('Location: ' . $verifySelfPath . '?err=server');
        exit;
    }
}

$verified = ((string) ($_GET['verified'] ?? '') === '1');
$recordId = (int) ($_GET['id'] ?? 0);
$searchValue = trim((string) ($_GET['q'] ?? ''));

if ($verified && $recordId > 0) {
    try {
        $record = verify_find_by_id($recordId);
        if (!is_array($record)) {
            $errorMessage = 'Visa record not found.';
            $verified = false;
        }
    } catch (Throwable $error) {
        $errorMessage = 'Unable to load visa details right now.';
        $verified = false;
    }
}

if ($errorMessage === '') {
    $errCode = trim((string) ($_GET['err'] ?? ''));
    if ($errCode === 'not_found') {
        $errorMessage = 'No New Zealand visa found for this number.';
    } elseif ($errCode === 'server') {
        $errorMessage = 'Server error. Please try again.';
    }
}

$remarks = json_decode((string) ($record['remarks'] ?? ''), true);
if (!is_array($remarks)) {
    $remarks = [];
}

$applicantImageUrl = verify_applicant_image_url((string) ($record['applicant_image'] ?? ''));
$loadedRecordId = (int) ($record['id'] ?? 0);
$statusMeta = verify_status_meta((string) ($record['status'] ?? ''));

$visaFields = [
    'Visa Number' => verify_record_or_remarks($record ?? [], $remarks, ['visa_number'], ['visaNumber']),
    'Reference Number' => verify_record_or_remarks($record ?? [], $remarks, ['ref_number'], ['refNumber', 'nzetaReferenceNumber']),
    'Full Name' => verify_record_or_remarks($record ?? [], $remarks, ['full_name'], ['name', 'fullName']),
    'Passport Number' => verify_record_or_remarks($record ?? [], $remarks, ['passport_number'], ['passportNumber', 'passportNo']),
    'Nationality' => verify_record_or_remarks($record ?? [], $remarks, ['nationality'], ['nationality', 'passportNationality']),
    'Visa Type' => verify_record_or_remarks($record ?? [], $remarks, ['visa_type'], ['visaType']),
    'Issue Date' => verify_format_date(verify_record_or_remarks($record ?? [], $remarks, ['issue_date'], ['issueDate'])),
    'Valid Until' => verify_format_date(verify_record_or_remarks($record ?? [], $remarks, ['evisa_expire_date'], ['validUntil', 'nzetaExpiryDate'])),
    'Place Of Issue' => verify_record_or_remarks($record ?? [], $remarks, ['place_of_issue'], ['issuedIn', 'placeOfIssue', 'visaIssuingAuthority']),
    'Entry Type' => verify_record_or_remarks($record ?? [], $remarks, [], ['entryType', 'entries']),
    'Employer' => verify_record_or_remarks($record ?? [], $remarks, [], ['employerName']),
    'Job Title' => verify_record_or_remarks($record ?? [], $remarks, [], ['jobTitle']),
];

$selfPath = (string) ($_SERVER['PHP_SELF'] ?? 'index.php');
$verificationUrl = verify_absolute_current_url(['verified' => '1', 'id' => (string) $loadedRecordId]);
$printQrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=0&data=' . rawurlencode($verificationUrl);

$debugPayload = [
    'record' => is_array($record) ? $record : null,
    'visa_fields' => $visaFields,
    'remarks' => $remarks,
    'applicant_image_url' => $applicantImageUrl,
    'verification_url' => $verificationUrl,
];

return compact(
    'searchValue', 'errorMessage', 'record', 'verified',
    'remarks', 'applicantImageUrl', 'loadedRecordId', 'statusMeta',
    'visaFields', 'selfPath', 'verificationUrl', 'printQrImageUrl', 'debugPayload'
);
}

if (!defined('NZ_VERIFY_INCLUDED')):
extract(verify_process_request());
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Zealand Immigration &mdash; Visa Verification</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Merriweather:wght@700&display=swap');

        :root {
            --bg: #ffffff;
            --ink: #101114;
            --muted: #3f434b;
            --line: #e3e4e8;
            --nz-dark: #00247d;
            --nz-red: #cc142b;
            --nz-green: #00843d;
            --nz-fern: #1b6d3f;
            --nz-header: #00247d;
            --card: #ffffff;
            --line-soft: #d4d6dc;
            --line-input: #c8ccd4;
            --surface-soft: #eef2fa;
            --surface-danger: #fff5f7;
            --surface-success: #edf7f0;
            --surface-warning: #f6f6f7;
            --surface-photo: #f3f4f7;
            --text-soft: #5c616b;
            --text-detail-label: #505560;
            --text-detail-value: #17181d;
            --focus-ring: rgba(0, 36, 125, 0.15);
            --shadow: 0 20px 45px rgba(14, 16, 20, 0.1);
            --radius-lg: 18px;
            --radius-md: 12px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: radial-gradient(circle at 90% 10%, var(--surface-soft) 0, var(--bg) 35%), linear-gradient(180deg, var(--bg) 0%, var(--bg) 100%);
            color: var(--ink);
            font-family: 'Manrope', sans-serif;
            min-height: 100vh;
        }

        .site-header {
            background: var(--nz-header);
            border-bottom: 1px solid var(--line);
        }

        .site-header-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 22px 18px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            gap: 14px;
        }

        .crest {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            border: 3px solid rgba(255,255,255,0.3);
            overflow: hidden;
            flex-shrink: 0;
        }

        .crest img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .title {
            margin: 0;
            font-family: 'Merriweather', serif;
            font-size: 2.2rem;
            line-height: 1.2;
            color: #fff;
        }

        .subtitle {
            margin: 4px 0 0;
            color: rgba(255,255,255,0.85);
            font-size: 0.94rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .accent-line {
            height: 4px;
            background: linear-gradient(90deg, var(--nz-dark) 0%, var(--nz-red) 50%, var(--nz-dark) 100%);
        }

        .page {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 18px 34px;
        }

        .alert-error {
            margin: 0 auto 18px;
            max-width: 760px;
            border: 1px solid #efc7d0;
            background: var(--surface-danger);
            color: #8f0b22;
            border-radius: 12px;
            padding: 12px 14px;
            font-weight: 600;
        }

        .search-card {
            max-width: 760px;
            margin: 0 auto;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 26px;
            box-shadow: var(--shadow);
        }

        .search-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--nz-dark);
        }

        .search-note {
            margin: 6px 0 18px;
            color: var(--muted);
            font-size: 0.96rem;
        }

        .search-input-wrap {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 10px;
            border: 2px solid var(--line-input);
            border-radius: 16px;
            padding: 8px 10px;
            background: var(--bg);
            margin-bottom: 12px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .search-input-wrap:focus-within {
            border-color: var(--nz-dark);
            box-shadow: 0 0 0 3px var(--focus-ring);
        }

        .search-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--surface-soft);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--nz-dark);
            flex-shrink: 0;
        }

        .search-input {
            width: 100%;
            border: none;
            padding: 12px 8px;
            font-size: 1rem;
            outline: none;
            background: transparent;
        }

        .verify-btn {
            border: none;
            border-radius: 11px;
            padding: 14px 22px;
            font-weight: 800;
            font-size: 0.95rem;
            background: linear-gradient(180deg, var(--nz-dark), #001a5e);
            color: #fff;
            cursor: pointer;
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.15s ease, filter 0.15s ease;
        }

        .verify-btn:hover { filter: brightness(1.1); transform: translateY(-1px); }

        .result-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .verified-banner {
            padding: 18px 20px 8px;
            text-align: center;
        }

        .verified-icon {
            width: 74px;
            height: 74px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: var(--nz-green);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0, 132, 61, 0.28);
        }

        .verified-text {
            margin: 0;
            color: var(--nz-fern);
            font-weight: 800;
            font-size: 1.85rem;
        }

        .result-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(120deg, var(--bg) 0%, var(--surface-soft) 100%);
            flex-wrap: wrap;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.87rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .status-approved { background: var(--surface-success); color: var(--nz-fern); border: 1px solid #b2dfc3; }
        .status-pending { background: var(--surface-warning); color: var(--ink); border: 1px solid #d8dbe0; }
        .status-rejected { background: #fdecef; color: #8f0b22; border: 1px solid #eecbd2; }

        .result-main {
            display: flex;
            flex-direction: column;
        }

        .left-panel {
            border-bottom: 1px solid var(--line);
            padding: 22px 18px;
            background: linear-gradient(180deg, var(--bg) 0%, var(--surface-soft) 100%);
        }

        .photo-wrap {
            width: 100%;
            max-width: 215px;
            margin: 0 auto;
            aspect-ratio: 3 / 4;
            border-radius: 14px;
            border: 1px solid var(--line-soft);
            overflow: hidden;
            background: var(--surface-photo);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .photo-wrap img { width: 100%; height: 100%; object-fit: cover; }

        .photo-placeholder {
            color: var(--text-soft);
            font-weight: 700;
            font-size: 0.9rem;
            text-align: center;
            padding: 12px;
        }

        .right-panel { padding: 22px 20px; }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 16px;
        }

        .detail {
            border-bottom: 1px dashed #dde0e7;
            padding-bottom: 10px;
            min-height: 60px;
        }

        .detail-label {
            display: block;
            color: var(--text-detail-label);
            font-size: 0.79rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .detail-value {
            display: block;
            color: var(--text-detail-value);
            font-size: 0.98rem;
            font-weight: 700;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .qr-section {
            text-align: center;
            padding: 18px;
            border-top: 1px solid var(--line);
        }

        .qr-section img {
            width: 140px;
            height: 140px;
            border: 1px solid var(--line-soft);
            border-radius: 10px;
            padding: 6px;
            background: #fff;
        }

        .qr-section p {
            margin: 8px 0 0;
            font-size: 0.82rem;
            color: var(--text-soft);
        }

        @media (max-width: 680px) {
            .title { font-size: 1.7rem; }
            .details-grid { grid-template-columns: 1fr; }
            .verify-btn { width: 100%; }
        }

        @media print {
            @page { size: A4 portrait; margin: 5mm; }
            .site-header, .search-card, .alert-error { display: none !important; }
            html, body { width: 210mm; min-height: 297mm; overflow: hidden; }
            .page { margin: 0; padding: 0; max-width: none; width: 100%; }
        }
    </style>
</head>

<body>
    <header class="site-header">
        <div class="site-header-inner">
            <span class="crest">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Flag_of_New_Zealand.svg/120px-Flag_of_New_Zealand.svg.png"
                    alt="New Zealand Flag">
            </span>
            <div>
                <h1 class="title">New Zealand Government</h1>
                <p class="subtitle">Immigration New Zealand &mdash; Visa Verification System</p>
            </div>
        </div>
        <div class="accent-line"></div>
    </header>

    <main class="page">
        <?php if ($errorMessage !== ''): ?>
            <div class="alert-error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!$verified || !is_array($record)): ?>
            <section class="search-card">
                <h2 class="search-title">Verify New Zealand Visa Record</h2>
                <p class="search-note">Enter Passport Number, Visa Number, or Reference Number to retrieve official verification details.</p>
                <form method="POST" action="<?php echo htmlspecialchars($selfPath, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="search-input-wrap">
                        <span class="search-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2H4V6Zm0 4h16v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8Zm3 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm4 0h6v2h-6v-2Z" />
                            </svg>
                        </span>
                        <input class="search-input" type="text" name="visa_number"
                            value="<?php echo htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="Passport Number or Visa Number" required>
                    </div>
                    <button type="submit" class="verify-btn">
                        <svg style="width:24px;height:24px;" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M12 2 4 5v6c0 5.2 3.3 9.9 8 11 4.7-1.1 8-5.8 8-11V5l-8-3Zm-1 13-3-3 1.4-1.4L11 12.2l3.6-3.6L16 10l-5 5Z" />
                        </svg>
                        VERIFY VISA
                    </button>
                </form>
            </section>
        <?php else: ?>
            <section class="result-card">
                <div class="verified-banner">
                    <span class="verified-icon" aria-hidden="true">
                        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 7 10 17l-6-6" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <h2 class="verified-text">Visa Verified</h2>
                </div>

                <div class="result-head">
                    <span class="badge <?php echo htmlspecialchars($statusMeta['class'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($statusMeta['label'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>

                <div class="result-main">
                    <aside class="left-panel">
                        <div class="photo-wrap">
                            <?php if ($applicantImageUrl !== ''): ?>
                                <img src="<?php echo htmlspecialchars($applicantImageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Applicant Photo">
                            <?php else: ?>
                                <div class="photo-placeholder">Applicant Photo Unavailable</div>
                            <?php endif; ?>
                        </div>
                    </aside>

                    <section class="right-panel">
                        <div class="details-grid">
                            <?php foreach ($visaFields as $label => $value): ?>
                                <?php $finalValue = trim((string) $value); ?>
                                <div class="detail">
                                    <span class="detail-label"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="detail-value"><?php echo htmlspecialchars($finalValue !== '' ? $finalValue : '-', ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </div>

                <div class="qr-section">
                    <img src="<?php echo htmlspecialchars($printQrImageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Verification QR Code">
                    <p>Scan to view this visa holder data</p>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php if ($verified && is_array($record)): ?>
        <script>
            var debugData = <?php echo json_encode($debugPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            console.log('all_data:', debugData);
        </script>
    <?php endif; ?>

</body>
</html>
<?php endif; ?>
