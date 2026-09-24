<?php
/**
 * core/Router.php
 * Minimal front-controller router.
 * Maps GET/POST URI patterns to Controller@method strings.
 */
class Router
{
    private array $routes = [];

    public function get(string $uri, string $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, string $action): void
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch(string $uri, string $method): void
    {
        // 1. Strip query string
        $uri = strtok($uri, '?');

        // 2. Strip subfolder prefix automatically.
        //    If the app lives at /moveup/public/, SCRIPT_NAME is
        //    /moveup/public/index.php → dirname = /moveup/public
        //    So /moveup/public/dashboard → /dashboard
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($scriptDir !== '' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // 3. Normalise: always exactly one leading slash
        $uri = '/' . ltrim($uri, '/');

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $pattern => $action) {
            // Turn :param into a named capture group
            $regex = preg_replace('/:([a-z_]+)/', '(?P<$1>[^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$controllerClass, $methodName] = explode('@', $action);

                // Autoload controller
                $file = __DIR__ . '/../app/controllers/' . $controllerClass . '.php';
                if (!file_exists($file)) {
                    $this->abort(500, "Controller file not found: $controllerClass");
                }
                require_once $file;

                if (!class_exists($controllerClass)) {
                    $this->abort(500, "Class not found: $controllerClass");
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $methodName)) {
                    $this->abort(500, "Method not found: $controllerClass@$methodName");
                }

                $controller->$methodName(...array_values($params));
                return;
            }
        }

        $this->abort(404, "Page introuvable &mdash; <code>$uri</code>");
    }

    private function abort(int $code, string $msg): void
    {
        http_response_code($code);
        echo "<div style='font-family:monospace;padding:2rem;background:#0d1310;color:#f87171'>
              <strong>$code</strong> — $msg</div>";
        exit;
    }
}
