<?php
// public/config.php
require_once(__DIR__ . '/../app/Controllers/ConfigController.php');

$controller = new ConfigController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        $controller->getApiKey();
        break;
    case 'set':
        $controller->setApiKey();
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
