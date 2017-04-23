<?php

namespace Swing\Tests;

use PHPUnit\Framework\TestCase;
use Swing\Router;

class RouterTest extends TestCase
{
    public function testRouterMatchingNonExistent()
    {
        $router = new Router('GET', '/nonexistent');
        $router->map('GET', '/news/[:id]', ['News', 'get']);

        $this->assertFalse($router->match());
    }

    public function testRouterMatchingOnGet()
    {
        $router = new Router('GET', '/News/1');

        $router->map('POST', '/news/:id', 'News#update');
        $router->map('POST', '/news', 'News#create');
        $router->map('GET', '/news/[:id]', ['News', 'get']);
        $router->map('DELETE', '/news/:id', 'News#delete');

        $actualRoute = [
            'method' => 'GET',
            'path' => ['news', '[:id]'],
            'action' => ['News', 'get'],
            'url_path' => ['news', '1'],
        ];

        $this->assertEquals($router->match(), $actualRoute);
    }
}
