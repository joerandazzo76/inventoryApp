<?php
// public/bins.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/Controllers/BinController.php';
$controller = new BinController($pdo);
$action = $_POST['action'] ?? $_GET['action'] ?? null;
if ($action === 'save') $controller->save();
if ($action === 'delete') $controller->delete();
$controller->index();
