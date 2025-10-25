<?php
// app/helpers.php

function base_url(): string {
    $config = require __DIR__ . '/config.php';
    return rtrim($config['app']['base_url'], '/') . '/';
}

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}
function csrf_check($token): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

// Add your helper functions here
// For example:

function debug($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function redirect($url) {
    header("Location: $url");
    exit;
}