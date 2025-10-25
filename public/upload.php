<?php
// public/upload.php
require_once __DIR__ . '/../app/helpers.php';
$config = require __DIR__ . '/../app/config.php';
$csrf = $_POST['csrf'] ?? '';
if (!csrf_check($csrf)) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad CSRF']);
    exit;
}
header('Content-Type: application/json');

if (!isset($_FILES['file'])) {
    echo json_encode(['error' => 'No file']);
    exit;
}
$updir = $config['app']['upload_dir'];
if (!is_dir($updir)) mkdir($updir, 0777, true);

$name = basename($_FILES['file']['name']);
$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
$allowed = ['jpg','jpeg','png','webp','gif'];
if (!in_array($ext, $allowed)) {
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

$dest = $updir . '/' . uniqid('img_', true) . '.' . $ext;
if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
    echo json_encode(['error' => 'Upload failed']);
    exit;
}

$url = $config['app']['upload_url'] . '/' . basename($dest);
echo json_encode(['path' => $dest, 'url' => $url]);
