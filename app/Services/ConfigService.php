<?php

namespace App\Services;

class ConfigService
{
    /**
     * Full configuration array.
     */
    private ?array $data = null;

    /**
     * Load config only when needed (lazy loading).
     */
    private function load(): void
    {
        if ($this->data === null) {
            $this->data = require __DIR__ . '/../../config.php';
        }
    }

    /**
     * Retrieve a value by dot-notation key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->load();

        $segments = explode('.', $key);
        $value = $this->data;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Return all registered parser extensions.
     */
    public function getParsers(): array
    {
        return $this->get('parsers', []);
    }
}