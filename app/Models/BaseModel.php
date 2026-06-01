<?php
require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/../Core/Database.php';

abstract class BaseModel extends Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY id ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        return $item ?: null;
    }

    public function delete(int $id): array
    {
        $stmt = $this->db->prepare('DELETE FROM ' . $this->table . ' WHERE id = :id');
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Запис не знайдено'];
        }
        $this->exportToJson();
        return ['success' => true, 'message' => 'Видалено'];
    }

    public function exportToJson(): void
    {
        $items = $this->getAll();
        if ($this->table === 'users') {
            foreach ($items as &$item) {
                unset($item['password']);
            }
            unset($item);
        }
        $this->saveData($this->table . '.json', $items);
    }
}
