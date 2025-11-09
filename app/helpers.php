<?php
// app/helpers.php

if (!defined('BASE_PATH')) {
    $base = realpath(__DIR__ . '/..');
    define('BASE_PATH', $base === false ? dirname(__DIR__) : $base);
}

function normalize_path(string $path): string
{
    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
}

function is_absolute_path(string $path): bool
{
    if ($path === '') {
        return false;
    }

    $first = $path[0];
    if ($first === '/' || $first === '\\') {
        return true;
    }

    if (preg_match('#^[A-Za-z]:[\\\\/]#', $path) === 1) {
        return true;
    }

    return strncmp($path, 'phar://', 7) === 0;
}

function join_path(string $root, string $path): string
{
    if (is_absolute_path($path)) {
        return normalize_path($path);
    }

    return $root . DIRECTORY_SEPARATOR . ltrim(normalize_path($path), DIRECTORY_SEPARATOR);
}

function base_path(string $path = ''): string
{
    if ($path === '') {
        return BASE_PATH;
    }

    return join_path(BASE_PATH, $path);
}

function app_path(string $path = ''): string
{
    $app = base_path('app');
    if ($path === '') {
        return $app;
    }

    return join_path($app, $path);
}

function public_path(string $path = ''): string
{
    $public = base_path('public');
    if ($path === '') {
        return $public;
    }

    return join_path($public, $path);
}

function view_path(string $path): string
{
    return join_path(public_path('views'), $path);
}

function base_url(): string
{
    static $config;
    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }
    return rtrim($config['app']['base_url'], '/') . '/';
}

function h($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check($token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

function debug($data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function redirect($url): void
{
    header("Location: $url");
    exit;
}
