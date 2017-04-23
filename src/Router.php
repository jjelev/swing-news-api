<?php

namespace Swing;

use Closure;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionObject;
use ReflectionParameter;
use UnexpectedValueException;

class Router
{
    const REQUIRED_REGEX = '/^:\w+/i';
    const OPTIONAL_REGEX = '/^\[:\w+]$/i';

    const MODELS_NAMESPACE = '\\Swing\\Models\\';
    const CONTROLLERS_NAMESPACE = '\\Swing\\Controllers\\';

    protected $routes;

    private $httpMethod;
    private $requestUri;

    function __construct(?string $method = null, ?string $uri = null)
    {
        $this->httpMethod = $method ?? $_SERVER['REQUEST_METHOD'];
        $this->requestUri = $uri ?? $_SERVER['REQUEST_URI'];
    }

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
        $uriComponents = $this->decomposePath(parse_url($this->requestUri, PHP_URL_PATH));

        // Main Routes Loop
        foreach ($this->routes as $route) {
            //Not fitting the route or not the http method we need
            if (count($uriComponents) > count($route['path']) ||
                strcasecmp($route['method'], $this->httpMethod) !== 0
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
        $keys = [];

        foreach ($routePath as $k => $routeCmp) {
            $uriCmp = $urlPath[$k] ?? null;

            if ($routeCmp !== $uriCmp) {
                $keys[] = $uriCmp;
            }
        }

        return $keys;
    }

    /**
     * @param array $params
     * @param array $keys
     * @return array
     */
    protected function getActionParameters(array $params, array $keys): array
    {
        $methodKeys = [];

        foreach ($params as $param) {
            if (!($param instanceof ReflectionParameter)) {
                throw new UnexpectedValueException('$param expects to be type of ReflectionParameter');
            }

            // If action parameter has no type definition
            if ($param->hasType() === false) {
                $methodKeys[] = array_shift($keys);
                continue;
            }

            $paramType = $param->getType();

            // Cast to built in PHP parameters or create new object (example: new Request())
            if ($paramType->isBuiltin()) {
                $key = array_shift($keys);

                // Allow passing nullable types to actions
                if ($key !== null || $paramType->allowsNull() === false) {
                    // Set correct type for the action parameter
                    settype($key, $paramType);
                }

                $methodKeys[] = $key;

            } else {
                $classNamespace = strval($paramType);

                if (!class_exists($classNamespace)) {
                    throw new InvalidArgumentException("Class {$classNamespace} does not exist.");
                }

                $methodKeys[] = new $classNamespace();
            }
        }

        return $methodKeys;
    }

    /**
     * Dynamically dispatches action
     *
     * @param array $route
     * @return mixed
     */
    public function dispatchAction(array $route)
    {
        $action = $route['action'];

        $keys = $this->getRouteVariables($route['path'], $route['url_path']);

        if ($action instanceof Closure) {

            $reflection = new ReflectionFunction($action);
            $params = $reflection->getParameters();

            if (count($params) > count($keys)) {
                throw new InvalidArgumentException('Some function arguments don\'t exist in route path');
            }

            return $action(...$keys);
        }

        list($className, $actionName) = is_array($action) ? $action : explode('#', $action);

        //The Place where magic(or bullshit) happens

        //Create Controller
        $classNamespace = self::CONTROLLERS_NAMESPACE . $className;

        if (!class_exists($classNamespace)) {
            throw new InvalidArgumentException("Controller {$classNamespace} does not exist.");
        }

        $controller = new $classNamespace();

        //Dispatch Action section
        if (!method_exists($controller, $actionName)) {
            throw new InvalidArgumentException("Method {$actionName} does not exist in {$classNamespace}");
        }

        //Get Controller Method parameters
        $reflectionObj = new ReflectionObject($controller);
        $method = $reflectionObj->getMethod($actionName);
        $params = $method->getParameters();

        return $controller->{$actionName}(...$this->getActionParameters($params, $keys));
    }
}