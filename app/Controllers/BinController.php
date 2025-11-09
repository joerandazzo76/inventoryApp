<?php
// app/Controllers/BinController.php
require_once __DIR__ . '/../Models/Bin.php';
require_once __DIR__ . '/../helpers.php';

class BinController {
    private BinModel $bins;
    public function __construct(PDO $db) { $this->bins = new BinModel($db); }

    public function index() {
        $list = $this->bins->all();
        include view_path('bins_index.php');
    }

    public function save() {
        if (!csrf_check($_POST['csrf'] ?? '')) die('Bad CSRF');
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $bin_number = (int)($_POST['bin_number'] ?? 0);
        $category = trim((string)($_POST['category'] ?? '')) ?: null;
        $notes = trim((string)($_POST['notes'] ?? '')) ?: null;

        if ($id) $this->bins->update($id, $bin_number, $category, $notes);
        else $this->bins->create($bin_number, $category, $notes);

        redirect(base_url() . 'bins.php');
    }

    public function delete() {
        if (!csrf_check($_POST['csrf'] ?? '')) die('Bad CSRF');
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $this->bins->delete($id);
        redirect(base_url() . 'bins.php');
    }
}
