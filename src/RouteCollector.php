<?php

namespace Light;

/**
 * Class RouteCollector
 *
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