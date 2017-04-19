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

        if ($route) {
            try {
                $this->router->dispatchAction($route);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo 'Nothing here';
        }
    }

    private function registerRoutes(): void
    {
        $this->router->map('GET', '/news/[:id]', ['News', 'get']);
//        $this->router->map('GET', '/news/[:id]', function ($id) {
//        });
        $this->router->map('POST', '/news/:id', 'News#update');
        $this->router->map('POST', '/news', 'News#create');
        $this->router->map('DELETE', '/news/:id', 'News#delete');
    }
}