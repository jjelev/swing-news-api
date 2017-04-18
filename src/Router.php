<?php

namespace Swing;

class Router
{
    const REQUIRED_REGEX = '/^:\w+/i';
    const OPTIONAL_REGEX = '/^\[:\w+]$/i';

    protected $routes;

    public function map(string $method, string $path, $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->decomposePath($path),
            'action' => $action,
        ];
    }

    public function match()
    {
        $uriComponents = $this->decomposePath(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        // Main Routes Loop
        foreach ($this->routes as $route) {
            //Not fitting the route or not the http method we need
            if (count($uriComponents) > count($route['path']) ||
                strcasecmp($route['method'], $_SERVER['REQUEST_METHOD']) !== 0
            ) {
                continue;
            }

            // Path Loop
            foreach ($route['path'] as $k => $routeCmp) {
                $uriCmp = $uriComponents[$k] ?? '';

                // check if path part matches the route syntax part, mandatory or optional keys
                if ($routeCmp === $uriCmp ||
                    (preg_match(self::REQUIRED_REGEX, $routeCmp) === 1 && $uriCmp !== '') ||
                    preg_match(self::OPTIONAL_REGEX, $routeCmp) === 1
                ) {
                    continue;
                }

                // Continue to next route (main loop)
                continue 2;
            }

            return $route;
        }

        //@TODO: return not found or 404 route
        return false;
    }

    protected function decomposePath(string $path): array
    {
        return explode('/', trim(strtolower($path), '/'));
    }

    protected function processAction($action)
    {

    }
}