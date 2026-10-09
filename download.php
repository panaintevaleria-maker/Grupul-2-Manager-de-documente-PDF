<?php
require_once 'functions.php';

$file = basename($_GET['file'] ?? '');
$path = DOC_DIR . '/' . $file;

if ($file === '' || !is_file($path) || !estePdf($file)) {
    header('Location: index.php');
    exit;
}

header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($path));

readfile($path);
exit;
