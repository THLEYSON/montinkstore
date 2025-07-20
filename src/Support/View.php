<?php

namespace App\Support;

class View
{
    public static function render(string $path, array $data = [], string $title = null): void
    {
        $viewFile = __DIR__ . "/../View/{$path}.php";
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View not found: {$viewFile}";
            exit;
        }

        extract($data);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }
}
