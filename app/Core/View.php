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
    public static function render(string $view, array $data = []): string
    {
        extract($data);

        ob_start();

        require __DIR__ . '/../Views/' . $view . '.php';

        return ob_get_clean();
    }
}