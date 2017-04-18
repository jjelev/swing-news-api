<?php

namespace Swing;

use Closure;
use Exception;

class Router
{
    const REQUIRED_REGEX = '/^:\w+/i';
    const OPTIONAL_REGEX = '/^\[:\w+]$/i';

    protected $routes;

    /**
     * Store route in router object
     *
     * @param string $method
     * @param string $path
     * @param $action
     */
    public function map(string $method, string $path, $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->decomposePath($path),
            'action' => $action,
        ];
    }

    /**
     * Matches and returns current route, else returns false
     *
     * @return array|bool
     */
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

                if ($this->pathComponentMatches($routeCmp, $uriComponents[$k] ?? null)) {
                    continue;
                }

                // Continue to next route (main loop)
                continue 2;
            }

            $route['url_path'] = $uriComponents;

            return $route;
        }

        return false;
    }

    /**
     * Creates array of URI Path Components
     *
     * @param string $path
     * @return array
     */
    protected function decomposePath(string $path): array
    {
        return explode('/', trim(strtolower($path), '/'));
    }

    /**
     * Check if string matches key or optional key syntax
     *
     * @param string $component
     * @param bool $optional
     * @return bool
     */
    protected function isKey(string $component, bool $optional = false): bool
    {
        $pattern = $optional ? self::OPTIONAL_REGEX : self::REQUIRED_REGEX;

        return preg_match($pattern, $component) === 1;
    }

    /**
     * Check if path part matches the route syntax part, mandatory or optional key
     *
     * @param string $routeCmp
     * @param null|string $uriCmp
     * @return bool
     */
    protected function pathComponentMatches(string $routeCmp, ?string $uriCmp): bool
    {
        return $routeCmp === $uriCmp || ($this->isKey($routeCmp) && $uriCmp !== null) || $this->isKey($routeCmp, true);
    }

    protected function getRouteVariables(array $routePath, array $urlPath): array
    {
        //TODO: Different diff algorithm, optional key must be in the array as empty string or null
        $keys = array_diff($urlPath, $routePath);

        return $keys;
    }

    public function dispatchAction(array $route)
    {
        $action = $route['action'];

        $keys = $this->getRouteVariables($route['path'], $route['url_path']);

        if ($action instanceof Closure) {

            $reflection = new \ReflectionFunction($action);
            $params = $reflection->getParameters();

            if (count($params) > count($keys)) {
                throw new Exception('Some function arguments don\'t exist in route path');
            }

            return $action(...$keys);
        }

        //TODO: Dispatch Controller Actions
    }
}