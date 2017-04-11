<?php

namespace Swing;

class Kernel
{
    public function run()
    {
        $this->registerRoutes();

        var_dump(self::class);
        exit;
    }

    private function registerRoutes()
    {
        $router = new Router();
//        $router->map('GET', '/news/[:id]', function ($id) {});
//        $router->map('POST', '/news/:id', 'News#update' {});

        $result = $router->match();
    }
}