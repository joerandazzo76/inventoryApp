<?php
// app/Controllers/ItemController.php
require_once __DIR__ . '/../Models/Item.php';
require_once __DIR__ . '/../Models/Bin.php';
require_once __DIR__ . '/../helpers.php';

class ItemController {
    private ItemModel $items;
    private BinModel $bins;
    public function __construct(PDO $db) {
        $this->items = new ItemModel($db);
        $this->bins = new BinModel($db);
    }

    public function index() {
        $list = $this->items->all();
        $bins = $this->bins->all();
        include __DIR__ . '/../../html/views/items_index.php';
    }

    public function save() {
        if (!csrf_check($_POST['csrf'] ?? '')) die('Bad CSRF');
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        $data = [
            'title' => trim((string)($_POST['title'] ?? '')) ?: null,
            'description' => trim((string)($_POST['description'] ?? '')) ?: null,
            'vendor' => trim((string)($_POST['vendor'] ?? '')) ?: null,
            'price' => $_POST['price'] !== '' ? (float)$_POST['price'] : null,
            'product_id' => trim((string)($_POST['product_id'] ?? '')) ?: null,
            'vendor_url' => trim((string)($_POST['vendor_url'] ?? '')) ?: null,
            'quantity' => $_POST['quantity'] !== '' ? (int)$_POST['quantity'] : 1,
            'bin_id' => $_POST['bin_id'] !== '' ? (int)$_POST['bin_id'] : null,
            'image_path' => trim((string)($_POST['image_path'] ?? '')) ?: null,
        ];

        if ($id) $this->items->update($id, $data);
        else $this->items->create($data);

        redirect(base_url() . 'items.php');
    }

    public function delete() {
        if (!csrf_check($_POST['csrf'] ?? '')) die('Bad CSRF');
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $this->items->delete($id);
        redirect(base_url() . 'items.php');
    }
}
