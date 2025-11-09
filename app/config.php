<?php
// app/config.php
// --- Basic app config ---
return [
    'db' => [
        // Example for SQL Server PDO:
        // On Windows, ensure you have the pdo_sqlsrv extension.
        // DSN format: "sqlsrv:Server=localhost,1433;Database=inventory_app;TrustServerCertificate=yes"
        'dsn' => $_ENV['DB_DSN'] ?? 'sqlsrv:Server=mssql,1433;Database=inventory_app;TrustServerCertificate=yes',
        'user' => $_ENV['DB_USER'] ?? 'sa',
        'pass' => $_ENV['DB_PASS'] ?? 'YourStrong!Passw0rd',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
    'app' => [
        'base_url' => $_ENV['BASE_URL'] ?? '/', // adjust if app is in a subfolder
        'upload_dir' => __DIR__ . '/../public/uploads',
        'upload_url' => '/uploads',
        'csrf_secret' => getenv('CSRF_SECRET') ?: 'change-me',
    ],
    // Category keywords -> bin suggestion (simple heuristics)
    'categories' => [
        1 => ['drone', 'quad', 'fpv', 'prop', 'esc', 'vtx', 'lip0', 'lip o', 'lip-', 'battery'],
        2 => ['gun', 'glock', 'barrel', 'mag', 'optic', 'holosun', 'trigger'],
        3 => ['3d', 'printer', 'nozzle', 'filament', 'bambu', 'elegoo', 'hotend'],
        4 => ['arduino', 'uno', 'nano', 'sensor', 'shield'],
        5 => ['raspberry', 'raspberrypi', 'raspberry-pi', 'pi', 'hat', 'gps hat', 'compute module'],
    ],
];
