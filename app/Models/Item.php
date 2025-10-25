<?php
// app/Models/Item.php
class ItemModel {
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function all(): array {
        $sql = 'SELECT items.*, bins.bin_number FROM items LEFT JOIN bins ON items.bin_id = bins.id ORDER BY items.created_at DESC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM items WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function create(array $data): int {
        $sql = 'INSERT INTO items (title, description, vendor, price, product_id, vendor_url, quantity, bin_id, image_path)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['title'] ?? null,
            $data['description'] ?? null,
            $data['vendor'] ?? null,
            $data['price'] ?? null,
            $data['product_id'] ?? null,
            $data['vendor_url'] ?? null,
            $data['quantity'] ?? 1,
            $data['bin_id'] ?? null,
            $data['image_path'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $data): bool {
        $sql = 'UPDATE items SET title=?, description=?, vendor=?, price=?, product_id=?, vendor_url=?, quantity=?, bin_id=?, image_path=? WHERE id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'] ?? null,
            $data['description'] ?? null,
            $data['vendor'] ?? null,
            $data['price'] ?? null,
            $data['product_id'] ?? null,
            $data['vendor_url'] ?? null,
            $data['quantity'] ?? 1,
            $data['bin_id'] ?? null,
            $data['image_path'] ?? null,
            $id
        ]);
    }
    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM items WHERE id=?');
        return $stmt->execute([$id]);
    }
}
