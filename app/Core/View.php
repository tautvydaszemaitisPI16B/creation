<?php

namespace App\Core;

class View
{
    /**
     * Renders a view template and returns the output as a string.
     *
     * @param string $view The view name (relative path without .php extension) located in the Views directory.
     * @param array  $data An associative array of variables to extract and make available within the view template.
     *
     * @return string The rendered HTML output of the view template.
     */
    public static function render(string $view, array $params = []): string
    {
        extract($params);

        ob_start();

        require_once __DIR__ . '/../Views/' . $view . '.php';

        $content = ob_get_clean();

        ob_start();

        require_once __DIR__ . '/../Views/layouts/main.php';

        return ob_get_clean();
    }
}