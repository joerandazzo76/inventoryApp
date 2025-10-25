<?php
// public/items.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/Controllers/ItemController.php';
require_once __DIR__ . '/../app/Controllers/VisionController.php';

$itemsCtrl = new ItemController($pdo);
$visionCtrl = new VisionController($pdo);

$action = $_POST['action'] ?? $_GET['action'] ?? null;
if ($action === 'save') $itemsCtrl->save();
if ($action === 'delete') $itemsCtrl->delete();
if ($action === 'autofill') { $visionCtrl->autoFill(); return; }

$itemsCtrl->index();
