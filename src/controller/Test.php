<?php

namespace Light\Controller;

/**
 * Class Test
 *
 * An example controller.  You can create a file called index.php and put this in it:
 *
 * <code>
 *     require_once __DIR__ . '/../vendor/autoload.php';
 *
 *     $app = new Light\App();
 *     $route = $app->routes;
 *
 *     $route->get('/test', Light\Controller\Test::class . '@index');
 * </code>
 *
 * Spin up a server:  php -S localhost:8080 -t public public/index.php
 *
 *
 * Then visit http://localhost/test
 *
 * @package Light\Controller
 */
class Test extends \Light\Controller
{
    /**
     * A test action
     *
     * @return array
     */
    public function index()
    {
        return ['foo' => 'bar'];
    }
}

