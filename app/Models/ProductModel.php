<?php
require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel
{
    protected $table = 'products';

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $category = trim($data['category'] ?? '');
        $price = floatval($data['price'] ?? 0);
        $description = trim($data['description'] ?? '');
        $stock = intval($data['stock'] ?? 0);
        $image = trim($data['image'] ?? 'https://via.placeholder.com/520x320?text=Фото');

        if ($id === 0) {
            $stmt = $this->db->prepare('INSERT INTO products (name, category, price, description, stock, image) VALUES (:name, :category, :price, :description, :stock, :image)');
            $stmt->execute(compact('name', 'category', 'price', 'description', 'stock', 'image'));
            $id = intval($this->db->lastInsertId());
        } else {
            $stmt = $this->db->prepare('UPDATE products SET name = :name, category = :category, price = :price, description = :description, stock = :stock, image = :image WHERE id = :id');
            $stmt->execute(compact('name', 'category', 'price', 'description', 'stock', 'image', 'id'));
        }

        $this->exportToJson();

        return ['success' => true, 'message' => 'Збережено', 'item' => [
            'id' => $id,
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'description' => $description,
            'stock' => $stock,
            'image' => $image
        ]];
    }
}
