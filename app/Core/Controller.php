<?php
class Controller
{
    protected function render(string $view, array $params = [])
    {
        extract($params, EXTR_SKIP);
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    protected function renderLayout(string $contentView, array $params = [])
    {
        $content = function () use ($contentView, $params) {
            extract($params, EXTR_SKIP);
            require __DIR__ . '/../Views/' . $contentView . '.php';
        };

        require __DIR__ . '/../Views/layout.php';
    }
}
