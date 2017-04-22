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

    /**
     * Routes registration
     *
     * Path Syntax:
     * :id - mandatory
     * [:id] - optional
     *
     * Action Syntax:
     * Controller#action
     * ['Controller', 'action']
     * Callback - for direct action execution
     */
    private function registerRoutes(): void
    {
//        $this->router->map('GET', '/news/[:id]', function ($id) {
//            $id++;
//            echo json_encode(['result' => $id]);
//        });

        $this->router->map('GET', '/news/[:id]', ['News', 'get']);
        $this->router->map('POST', '/news/:id', 'News#update');
        $this->router->map('POST', '/news', 'News#create');
        $this->router->map('DELETE', '/news/:id', 'News#delete');
    }
}