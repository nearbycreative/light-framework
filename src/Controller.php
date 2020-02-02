<?php

namespace Light;

/**
 * Class Controller
 *
 * @author Chris Page <chris@nearbycreative.com>
 * @package Light
 */
class Controller
{
    /**
     * @var App|null
     */
    public $app = null;

    /**
     * @var \Illuminate\Http\Request|null
     */
    public $request = null;

    /**
     * Controller constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = request();
    }
}