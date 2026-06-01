<?php
require_once __DIR__ . '/BaseModel.php';

class GalleryModel extends BaseModel
{
    protected $table = 'gallery';

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        $title = trim($data['title'] ?? '');
        $image = trim($data['image'] ?? 'https://via.placeholder.com/260x160?text=Фото');
        $caption = trim($data['caption'] ?? '');

        if ($id === 0) {
            $stmt = $this->db->prepare('INSERT INTO gallery (title, image, caption) VALUES (:title, :image, :caption)');
            $stmt->execute(compact('title', 'image', 'caption'));
            $id = intval($this->db->lastInsertId());
        } else {
            $stmt = $this->db->prepare('UPDATE gallery SET title = :title, image = :image, caption = :caption WHERE id = :id');
            $stmt->execute(compact('title', 'image', 'caption', 'id'));
        }

        $this->exportToJson();

        return ['success' => true, 'message' => 'Фотографію збережено', 'item' => [
            'id' => $id,
            'title' => $title,
            'image' => $image,
            'caption' => $caption
        ]];
    }
}
