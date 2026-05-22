<?php

namespace App\Core;

/**
 * HTTP request helper.
 *
 * Provides simple access to request input data
 * from both POST and GET requests.
 */
class Request
{
    /**
     * Get an input value from the request.
     *
     * Checks POST data first, then GET data.
     *
     * Example:
     * Request::input('email');
     *
     * Example with default value:
     * Request::input('page', 1);
     *
     * @param string $key Input field name.
     * @param mixed $default Default value if the key does not exist.
     *
     * @return mixed
     */
    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}