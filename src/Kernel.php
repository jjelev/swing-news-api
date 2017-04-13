<?php

namespace Swing;

class Kernel
{
    public function run()
    {
        $this->registerRoutes();
    }

    private function registerRoutes()
    {
        $router = new Router();

        $router->map('GET', '/news/[:id]', function ($id) {
        });
        $router->map('POST', '/news/:id', 'News#update');
        $router->map('POST', '/news', 'News#create');
        $router->map('DELETE', '/news/:id', 'News#delete');

        $router->match();
    }
}