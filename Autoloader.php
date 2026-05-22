<?php

declare(strict_types=1);

/**
 * Simple PSR-4 style autoloader.
 *
 * Registers namespaces and automatically loads PHP classes
 * based on their namespace and directory structure.
 *
 * Example:
 *
 * $loader = new Autoloader();
 * $loader->addNamespace('App\\', __DIR__ . '/src');
 * $loader->register();
 *
 * new App\Controllers\HomeController();
 */
class Autoloader
{
    /**
     * Namespace prefix to base directory mappings.
     *
     * Example:
     * [
     *     'App\\' => '/project/src'
     * ]
     *
     * @var array<string, string>
     */
    private array $prefixes = [];

    /**
     * Adds a namespace mapping.
     *
     * Example:
     * addNamespace('App\\', '/project/src');
     *
     * @param string $prefix Namespace prefix.
     * @param string $baseDir Base directory for the namespace.
     *
     * @return void
     */
    public function addNamespace(string $prefix, string $baseDir): void
    {
        $this->prefixes[$prefix] = rtrim($baseDir, '/');
    }

    /**
     * Registers the autoloader with SPL.
     *
     * @return void
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Loads a class file based on namespace mapping.
     *
     * Converts namespace separators "\" into directory separators "/"
     * and appends ".php" to the generated file path.
     *
     * Example:
     * App\Controllers\HomeController
     * becomes:
     * /project/src/Controllers/HomeController.php
     *
     * @param string $class Fully qualified class name.
     *
     * @return void
     */
    private function loadClass(string $class): void
    {
        foreach ($this->prefixes as $prefix => $baseDir) {
            if (str_starts_with($class, $prefix)) {
                $relativeClass = substr($class, strlen($prefix));

                $file = $baseDir
                    . str_replace('\\', '/', $relativeClass)
                    . '.php';

                if (file_exists($file)) {
                    require $file;
                }
            }
        }
    }
}