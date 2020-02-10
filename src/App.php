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


define('APP_PATH', realpath(__DIR__ . '/../../../'));


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
     * Holds all of the routes
     *
     * @var RouteCollector|null
     */
    public $routes = null;

    /**
     * @var Connection|null
     */
    public static $_connection = null;

    /**
     * @var App|null
     */
    public static $_instance = null;

    /**
     * Dispatch constructor.
     *
     * @param string $routes_path
     * @param string $response_content_type
     */
    public function __construct($routes_path = 'config/routes.php', $response_content_type = 'application/json')
    {
        $this->setResponseContentType($response_content_type);

        self::$_instance = $this;

        $this->routes = new RouteCollector();

        if ($routes_path) {
            $route = $this->routes;
            require_once $routes_path;
        }
    }

    /**
     * Return an app instance
     *
     * @param string $routes_path
     * @param string $response_content_type
     * @return App|null
     */
    public static function singleton($routes_path = 'config/routes.php', $response_content_type = 'application/json')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new App($routes_path, $response_content_type);
        }

        return self::$_instance;
    }

    /**
     * Includes a global helper methods
     *
     * @return void
     */
    public static function initGlobals()
    {
        require_once 'Global.php';
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
     * @return $this
     */
    public function setResponseContentType($contentType)
    {
        $this->response_content_type = $contentType;

        return $this;
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
     * This provides /[controller]/[action] paradigm as the default so that
     * you don't have to manually create every route for every controller if
     * you don't want to.
     *
     * It first looks to see if the URL is a controller path with the 'index' action ommitted
     * If it doesn't find that, it checks if the last segment in the URI was actually an action
     * If it finds either, it'll add it to the routes
     * If it finds none, the returned route error will produce a 404 messaage
     *
     *
     * @param $method
     * @param $uri
     * @param $pathInfo
     * @see App::run()
     * @return array
     */
    private function detectControllerRoute($method, $uri, $pathInfo)
    {
        $path = ucwords(rtrim($pathInfo, '/'), '/');

        $classPath = '\\App\\Controller' . str_replace('/', '\\', $path);

        if (class_exists($classPath) && method_exists($classPath, 'index')) {
            //This path is a controller with an index action that wasn't specified in the url
            $handler = $classPath . '@index';
        } else {
            //There was no index found at this controller path, so last segment of the URL may be an action instead
            $parts = explode('\\', $classPath);
            $action = array_pop($parts);
            $classPath = implode('\\', $parts);

            if(class_exists($classPath) && method_exists($classPath, $action)) {
                $handler = $classPath . '@' . strtolower($action);
            }
        }

        if (isset($handler)) {
            $this->routes->any($pathInfo, $handler);
        }

        return $this->getRouteInfo($method, $uri);
    }

    /**
     * Parses the URI, runs the corresponding route, and sends the response to the client
     *
     * Looks for a route closure first, if one is found, that will take precedent.
     * If a route closure didn't exist, check if there is a controller route.
     *
     * This also supports sub controllers.
     *
     * Optional parameters $method and $uri can be used to force the HTTP verb and URI instead of detecting them from the request.
     *
     * @see App::detectControllerRoute()
     *
     * @param null $method [optional] Can be used to force the method type, otherwise it's detected from the request
     * @param null $uri [optional] Can be used to force the $uri, otherwise it's detected from the request
     * @return array|string|null
     */
    public function run($method = null, $uri = null)
    {
        $method = $method ? strtoupper($method) : request()->method();
        $uri = $uri ? $uri : request()->getRequestUri();

        $routeInfo = $this->getRouteInfo($method, $uri);

        if ($routeInfo[0] === \FastRoute\Dispatcher::NOT_FOUND) {
            $routeInfo = $this->detectControllerRoute($method, $uri, request()->getPathInfo());
        }

        if($routeInfo[0] === \FastRoute\Dispatcher::FOUND) {
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            if (is_string($handler)) {
                list($class, $method) = explode('@', $handler);

                $instance = new $class();
                $instance->app = $this;
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

        //Force no cache
        header('Content-type:  ' . $this->response_content_type);
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

        //Make sure the response code is set in the header
        http_response_code($response['code']);

        if ($this->response_content_type === 'application/json') {

            echo json_encode($response, JSON_PRETTY_PRINT);
        } elseif ($this->response_content_type === 'application/xml') {
            echo xmlrpc_encode($response);
        } else {
            echo $response;
        }

        return $response;
    }
}