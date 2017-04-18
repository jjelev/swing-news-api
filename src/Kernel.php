<?php

namespace Swing;

class Kernel
{
    private $router;

    function __construct()
    {
        $this->router = new Router();
    }

    public function run(): void
    {
        $this->registerRoutes();

        $route = $this->router->match();
    }

    private function registerRoutes(): void
    {
        $this->router->map('GET', '/news/[:id]', function ($id) {
        });
        $this->router->map('POST', '/news/:id', 'News#update');
        $this->router->map('POST', '/news', 'News#create');
        $this->router->map('DELETE', '/news/:id', 'News#delete');
    }
}