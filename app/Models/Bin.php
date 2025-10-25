<?php
// app/Models/Bin.php
class BinModel {
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function all(): array {
        $stmt = $this->db->query('SELECT * FROM bins ORDER BY bin_number ASC');
        return $stmt->fetchAll();
    }
    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM bins WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function create(int $bin_number, ?string $category, ?string $notes): int {
        $stmt = $this->db->prepare('INSERT INTO bins (bin_number, category, notes) VALUES (?, ?, ?)');
        $stmt->execute([$bin_number, $category, $notes]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, int $bin_number, ?string $category, ?string $notes): bool {
        $stmt = $this->db->prepare('UPDATE bins SET bin_number=?, category=?, notes=? WHERE id=?');
        return $stmt->execute([$bin_number, $category, $notes, $id]);
    }
    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM bins WHERE id=?');
        return $stmt->execute([$id]);
    }
}
