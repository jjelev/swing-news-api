<?php

namespace Swing;

use Closure;

class Router
{
    public function map(string $method, string $path, Closure $callback)
    {

        return $callback;
    }

    public function match()
    {
        return true;
    }
}