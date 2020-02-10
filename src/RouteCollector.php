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

/**
 * Class RouteCollector
 *
 * @author Chris Page <chris@nearbycreative.com>
 * @package Light
 */
class RouteCollector extends \FastRoute\RouteCollector
{
    /**
     * Holds all the route mappings for the `light route:list` command
     *
     * @var array
     */
    public $map = [];

    /**
     * RouteCollector constructor.
     */
    public function __construct()
    {
        parent::__construct(
            new \FastRoute\RouteParser\Std, new \FastRoute\DataGenerator\GroupCountBased
        );
    }
    /**
     * Alias to addGroup
     *
     * @param $prefix
     * @param $closure
     */
    public function group($prefix, $closure)
    {
        parent::addGroup($prefix, $closure);
    }

    /**
     *
     * <code>
     *
     * </code>
     *
     * @param $methods
     * @param $route
     * @param $closure
     * @return $this
     */
    public function map($methods, $route, $closure)
    {
        foreach ($methods as $method) {
            $this->{strtolower($method)}($route, $closure);
        }

        return $this;
    }

    /**
     * @param $route
     * @param $closure
     * @return $this
     */
    public function any($route, $closure)
    {
        foreach (['GET','POST','PUT','PATCH','DELETE','HEAD','OPTIONS'] as $method) {
            $this->{strtolower($method)}($route, $closure);
        }

        return $this;
    }

    /**
     * Adds an OPTIONS route to the collection
     *
     * This is simply an alias of $this->addRoute('OPTIONS', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function options($route, $handler)
    {
        $this->addRoute('OPTIONS', $route, $handler);
    }

    /**
     * Adds a GET route to the collection
     *
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function get($route, $handler)
    {
        $this->addRoute('GET', $route, $handler);
    }

    /**
     * Adds a POST route to the collection
     *
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function post($route, $handler)
    {
        $this->addRoute('POST', $route, $handler);
    }

    /**
     * Adds a PUT route to the collection
     *
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function put($route, $handler)
    {
        $this->addRoute('PUT', $route, $handler);
    }

    /**
     * Adds a DELETE route to the collection
     *
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function delete($route, $handler)
    {
        $this->addRoute('DELETE', $route, $handler);
    }

    /**
     * Adds a PATCH route to the collection
     *
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function patch($route, $handler)
    {
        $this->addRoute('PATCH', $route, $handler);
    }

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function head($route, $handler)
    {
        $this->addRoute('HEAD', $route, $handler);
    }

    /**
     * @param string|string[] $method
     * @param string $route
     * @param mixed $handler
     */
    public function addRoute($method, $route, $handler)
    {
        $this->updateRouteMap($method, $route, $handler);

        parent::addRoute($method, $route, $handler);
    }

    /**
     * Updates the route map
     *
     * @param $method
     * @param $route
     */
    public function updateRouteMap($method, $route, $handler)
    {
        $this->map[$route][$method] = $handler;
    }

    /**
     * Redirect
     *
     * @param $route
     * @param $location
     * @param integer $code Defaults to 302
     * @return $this
     */
    public function redirect($route, $location, $code = 302)
    {
        $this->get($route, function() use ($location, $code) {
            header("Location: $location", true, $code);
            exit;
        });

        return $this;
    }

    public function routes()
    {
        return $this->dataGenerator;
    }
}