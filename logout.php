<?php
require_once __DIR__ . '/nz-auth.php';
nz_auth_logout();
$base = nz_base_path();
header('Location: ' . $base . '/index.php');
exit;
