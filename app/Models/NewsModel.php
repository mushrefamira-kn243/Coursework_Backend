<?php
require_once __DIR__ . '/BaseModel.php';

class NewsModel extends BaseModel
{
    protected $table = 'news';

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        $title = trim($data['title'] ?? '');
        $summary = trim($data['summary'] ?? '');
        $content = trim($data['content'] ?? '');
        $date = trim($data['date'] ?? date('Y-m-d'));
        $image = trim($data['image'] ?? 'https://via.placeholder.com/520x320?text=News');

        if ($id === 0) {
            $stmt = $this->db->prepare('INSERT INTO news (title, summary, content, date, image) VALUES (:title, :summary, :content, :date, :image)');
            $stmt->execute(compact('title', 'summary', 'content', 'date', 'image'));
            $id = intval($this->db->lastInsertId());
        } else {
            $stmt = $this->db->prepare('UPDATE news SET title = :title, summary = :summary, content = :content, date = :date, image = :image WHERE id = :id');
            $stmt->execute(compact('title', 'summary', 'content', 'date', 'image', 'id'));
        }

        return ['success' => true, 'message' => 'Новина збережена', 'item' => [
            'id' => $id,
            'title' => $title,
            'summary' => $summary,
            'content' => $content,
            'date' => $date,
            'image' => $image
        ]];
    }
}
