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
}