<?php

namespace App\Core;

use App\Http\Router;

/**
 * Main application class.
 *
 * Responsible for bootstrapping and running
 * the application lifecycle.
 */
class App
{
    /**
     * Application router instance.
     *
     * @var Router
     */
    public Router $router;

    /**
     * Create a new application instance.
     *
     * Initializes the router.
     */
    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * Run the application.
     *
     * Dispatches the current HTTP request
     * through the router and outputs the response.
     *
     * @return void
     */
    public function run(): void
    {
        echo $this->router->dispatch(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI']
        );
    }
}