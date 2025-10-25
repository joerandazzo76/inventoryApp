<?php
// app/db.php
$config = require __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        $config['db']['dsn'],
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['options']
    );
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>DB Connection Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

return $pdo;
