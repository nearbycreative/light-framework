<?php
/**
 * MIT License
 *
 * Copyright (c) 2020 Nearby Creative https://nearbycreative.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Light;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;

/**
 * Include helper methods
 */
require_once 'Global.php';

/**
 * Class App
 *
 * Built on FastRoute to map URI to closures.
 *
 * @author Chris Page <chris@nearbycreative.com>
 * @package Light
 */
class App
{
    /**
     * The response content type to send back to the browser
     *
     * @var string
     */
    private $response_content_type = 'application/json';

    /**
     * @var RouteCollector|null
     */
    public $routes = null;

    /**
     * @var Connection|null
     */
    public static $_connection = null;

    /**
     * Dispatch constructor.
     *
     * @param string $response_content_type
     */
    public function __construct($response_content_type = 'application/json')
    {
        $this->setResponseContentType($response_content_type);

        $this->routes = new RouteCollector();
    }

    /**
     * Boot the Eloquent connection
     */
    public function bootEloquent()
    {
        $capsule = new Capsule;

        $config = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => '',
            'username' => '',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''

        ];

        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$_connection = $capsule->getConnection();
    }

    /**
     * Fetch the Illuminate Schema Builder, ie:
     *
     * <code>
     *    if (App::schema()->hasTable('mytable')) {
     *        //do something
     *    }
     * </code>
     *
     * @return Builder|null
     */
    public static function schema()
    {
        if (self::$_connection) {
            return self::$_connection->getSchemaBuilder();
        } else {
            return null;
        }
    }

    /**
     * Set the response content type
     *
     * @see Dispatch::__construct()
     * @param string $contentType Default is set to 'application/json'
     */
    public function setResponseContentType($contentType)
    {
        $this->response_content_type = $contentType;
    }

    /**
     * Get route info based on the HTTP method provided and the URI
     *
     * @param $method
     * @param $uri
     * @return array
     */
    private function getRouteInfo($method, $uri)
    {
        $dispatcher = new \FastRoute\Dispatcher\GroupCountBased($this->routes->getData());

        return $dispatcher->dispatch($method, $uri);
    }

    /**
     * Looks for a matching controller route first
     * Then looks for a FastRoute, if one is found it overrides the controller route.
     *
     * This provides /[controller]/[action] paradigm as the default so that
     * you don't have to manually create every route for every controller if
     * you don't want to.
     *
     * @return array
     */
    private function detectControllerRoute()
    {
        $path = ucwords(request()->getPathInfo(), '/');
        $parts = explode('/', ltrim($path, '/'));

        $classPath = '\\App\\Controller\\';
        if ($parts[0]) {
            if (count($parts) === 1) {
                $classPath .= $parts[0];
            } elseif (count($parts) >= 2) {
                $classPath .= implode('\\', $parts);
            }
        } else {
            //it's the root URL, which we default to the Controller\Welcome@index
            $classPath .= 'Welcome';
        }

        //Check if this is sub controller index
        if (class_exists($classPath) && method_exists($classPath, 'index')) {
            $handler = $classPath . '@index';
            $this->routes->any(request()->getPathInfo(), $handler);
        } else {
            //It's not a sub controller index, the last segment may be an action, lets see if that exists
            $parts = explode('\\', $classPath);
            $action = array_pop($parts);
            $classPath = implode('\\', $parts);

            $handler = $classPath . '@' . $action;
            if (class_exists($classPath) && method_exists($classPath, $action)) {
                $this->routes->any(request()->getPathInfo(), $handler);
            }
        }

        return $this->getRouteInfo(request()->method(), request()->getRequestUri());
    }

    /**
     * Parses the URI, runs the corresponding route, and sends the response to the client
     *
     * Looks for a route closure first, if one is found, that will take precedent.
     * If a route closure didn't exist, check if there is a controller route.
     *
     * This provides /[controller]/[action] paradigm as the default so that
     * you don't have to manually create every route for every controller action if
     * you don't want to.
     *
     * This also supports sub controllers.
     */
    public function run()
    {
        $routeInfo = $this->getRouteInfo(request()->method(), request()->getRequestUri());

        if ($routeInfo[0] === \FastRoute\Dispatcher::NOT_FOUND) {
            $routeInfo = $this->detectControllerRoute();
        }

        if($routeInfo[0] === \FastRoute\Dispatcher::FOUND) {
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            if (is_string($handler)) {
                list($class, $method) = explode('@', $handler);

                $instance = new $class($this);
                $handler = [$instance, $method];
            }

            $response = [
                'code' => 200,
                'status' => 'success',
                'time' => \Carbon\Carbon::now(),
                'data' => call_user_func_array($handler, $vars)
            ];
        } elseif ($routeInfo[0] === \FastRoute\Dispatcher::NOT_FOUND) {
            $response = [
                'code' => 404,
                'status' => 'error',
                'time' => \Carbon\Carbon::now(),
                'message' => 'Not found.'
            ];
        } elseif ($routeInfo[0] === \FastRoute\Dispatcher::METHOD_NOT_ALLOWED) {
            $response = [
                'code' => 405,
                'status' => 'error',
                'time' => \Carbon\Carbon::now(),
                'message' => 'Method not allowed.',
                'allowedMethods' => $routeInfo[1]
            ];
        } else {
            $response = [
                'code' => 500,
                'status' => 'error',
                'time' => \Carbon\Carbon::now(),
                'message' => 'Unhandled response.'
            ];
        }

        header('Content-type:  ' . $this->response_content_type);
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        http_response_code($response['code']);

        if ($this->response_content_type === 'application/json') {

            echo json_encode($response, JSON_PRETTY_PRINT);
        } else {
            echo $response;
        }

    }
}