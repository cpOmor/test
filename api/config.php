<?php

(static function () {
    $path = __DIR__ . '/.env';
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $trimmed, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim(trim($parts[1]), "\"'");
        if ($key === '') {
            continue;
        }

        if (getenv($key) === false || getenv($key) === '') {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
})();

define('NZ_MYSQL_HOST', getenv('NZ_MYSQL_HOST') ?: '');
define('NZ_MYSQL_PORT', getenv('NZ_MYSQL_PORT') ?: '');
define('NZ_MYSQL_DB', getenv('NZ_MYSQL_DB') ?: '');
define('NZ_MYSQL_USER', getenv('NZ_MYSQL_USER') ?: '');
define('NZ_MYSQL_PASS', getenv('NZ_MYSQL_PASS') ?: '');
define('NZ_MYSQL_CHARSET', getenv('NZ_MYSQL_CHARSET') ?: '');
define('NZ_MYSQL_TABLE', getenv('NZ_MYSQL_TABLE') ?: '');
define('NZ_ALLOWED_ORIGIN', getenv('NZ_ALLOWED_ORIGIN') ?: '*');
define('NZ_DOMAIN', rtrim(getenv('NZ_DOMAIN') ?: '/'));
define('NZ_QR_SECRET', getenv('NZ_QR_SECRET') ?: 'change-this-public-qr-secret');
define('NZ_QR_TTL', getenv('NZ_QR_TTL') ?: '604800');

function nz_env(string $key, string $default = ''): string
{
    $value = getenv($key);
    if ($value === false && isset($_ENV[$key])) {
        $value = (string) $_ENV[$key];
    }
    if (($value === false || $value === '') && isset($_SERVER[$key])) {
        $value = (string) $_SERVER[$key];
    }

    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

function nz_load_env_file(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $trimmed, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);
        $value = trim($value, "\"'");

        if ($key === '') {
            continue;
        }

        if (getenv($key) === false || getenv($key) === '') {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

function nz_mysql_host(): string
{
    return NZ_MYSQL_HOST;
}

function nz_mysql_port(): string
{
    return NZ_MYSQL_PORT;
}

function nz_mysql_db(): string
{
    return NZ_MYSQL_DB;
}

function nz_mysql_user(): string
{
    return NZ_MYSQL_USER;
}

function nz_mysql_pass(): string
{
    return NZ_MYSQL_PASS;
}

function nz_mysql_charset(): string
{
    return NZ_MYSQL_CHARSET;
}

function nz_mysql_table(): string
{
    return NZ_MYSQL_TABLE;
}

function nz_allowed_origin(): string
{
    return NZ_ALLOWED_ORIGIN;
}

function nz_domain(): string
{
    return NZ_DOMAIN;
}

function nz_qr_secret(): string
{
    return NZ_QR_SECRET;
}

function nz_qr_ttl_seconds(): int
{
    $value = (int) NZ_QR_TTL;
    return $value > 0 ? $value : 604800;
}
