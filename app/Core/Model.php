<?php
class Model
{
    protected function loadData(string $filename): array
    {
        $path = __DIR__ . '/../../data/' . $filename;
        if (!file_exists($path)) {
            return [];
        }
        $content = file_get_contents($path);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    protected function saveData(string $filename, array $data): bool
    {
        $path = __DIR__ . '/../../data/' . $filename;
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($path, $json) !== false;
    }
}
