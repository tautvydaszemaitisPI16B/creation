<?php
namespace App\Http;

/**
 * Simple HTTP router.
 *
 * Handles route registration and dispatching
 * for incoming HTTP requests.
 */
class Router
{
    /**
     * Registered application routes.
     *
     * Example structure:
     * [
     *     'GET' => [
     *         '/' => callable,
     *         '/users' => [UserController::class, 'index']
     *     ]
     * ]
     *
     * @var array<string, array<string, callable|array>>
     */
    private array $routes = [];

    /**
     * Register a GET route.
     *
     * Example:
     * $router->get('/', fn () => 'Hello');
     *
     * Or:
     * $router->get('/users', [UserController::class, 'index']);
     *
     * @param string $path Route URI path.
     * @param callable|array $action Route action callback or controller action.
     *
     * @return void
     */
    public function get(string $path, callable|array $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    /**
     * Dispatches the incoming request.
     *
     * Finds a matching route and executes its action.
     *
     * @param string $method HTTP request method.
     * @param string $uri Requested URI.
     *
     * @return mixed
     */
    public function dispatch(string $method, string $uri): mixed
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        $action = $this->routes[$method][$uri] ?? null;

        if (!$action) {
            http_response_code(404);

            return '404 Not Found';
        }

        return is_callable($action)
            ? call_user_func($action)
            : $this->callController($action);
    }

    /**
     * Calls a controller action.
     *
     * Example:
     * [HomeController::class, 'index']
     *
     * @param array{0: class-string, 1: string} $action
     *
     * @return mixed
     */
    private function callController(array $action): mixed
    {
        [$class, $method] = $action;

        return (new $class)->$method();
    }
}