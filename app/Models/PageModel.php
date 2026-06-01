<?php
require_once __DIR__ . '/BaseModel.php';

class PageModel extends BaseModel
{
    protected $table = 'pages';

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        $slug = trim($data['slug'] ?? '');
        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');

        if ($id === 0) {
            $stmt = $this->db->prepare('INSERT INTO pages (slug, title, content) VALUES (:slug, :title, :content)');
            $stmt->execute(compact('slug', 'title', 'content'));
            $id = intval($this->db->lastInsertId());
        } else {
            $stmt = $this->db->prepare('UPDATE pages SET slug = :slug, title = :title, content = :content WHERE id = :id');
            $stmt->execute(compact('slug', 'title', 'content', 'id'));
        }

        $this->exportToJson();

        return ['success' => true, 'message' => 'Сторінку збережено', 'item' => [
            'id' => $id,
            'slug' => $slug,
            'title' => $title,
            'content' => $content
        ]];
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pages WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        return $page ?: null;
    }
}
