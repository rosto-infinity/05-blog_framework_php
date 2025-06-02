<?php
namespace Libraries;

class Renderer {

    public static function render(string $path, array $variables = [])
    {
        extract($variables);
        ob_start();

        // Chemin absolu vers le fichier de vue
        $viewFile = dirname(__DIR__) . '/views/' . $path . '_html.php';
        if (!file_exists($viewFile)) {
            throw new \Exception("Vue non trouvée : $viewFile");
        }
        require $viewFile;

        $pageContent = ob_get_clean();

        // Chemin absolu vers le layout
        $layoutFile = dirname(__DIR__) . '/views/layout_html.php';
        if (!file_exists($layoutFile)) {
            throw new \Exception("Layout non trouvé : $layoutFile");
        }
        require $layoutFile;
    }
}
