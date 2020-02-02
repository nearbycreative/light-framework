<?php

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
     * Uses FastRoute methods to build
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
     * Parses the URI, runs the corresponding route, and sends the response to the client
     */
    public function run() : void
    {
        $routeInfo = $this->getRouteInfo(request()->method(), request()->getRequestUri());

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
                'message' => 'Not found.'
            ];
        } elseif ($routeInfo[0] === \FastRoute\Dispatcher::METHOD_NOT_ALLOWED) {
            $response = [
                'code' => 405,
                'status' => 'error',
                'message' => 'Method not allowed.',
                'allowedMethods' => $routeInfo[1]
            ];
        } else {
            $response = [
                'code' => 500,
                'status' => 'error',
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