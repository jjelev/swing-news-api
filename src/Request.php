<?php

namespace Swing;

class Request
{
    private $request;

    function __construct()
    {
        $this->request = $_REQUEST;
    }

    public function all()
    {
        return $this->request;
    }

    public function input(?string $key = null, ?string $default = null)
    {
        if ($key === null) {
            return $this->all();
        }

        if (array_key_exists($key, $this->request)) {
            return $this->request[$key];
        }

        return $default;
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function isMethod(string $method)
    {
        return strtoupper($method) === $this->method();
    }
}