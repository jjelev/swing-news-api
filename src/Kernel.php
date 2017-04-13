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

        $router->map('GET', '/news/:id/[:date]', function ($id) {
        });
        $router->map('POST', '/news/:id', 'News#update');

        $router->match();
    }
}