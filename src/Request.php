<?php

namespace Light;

/**
 * Class Request
 *
 * @package Light
 */
class Request extends \Illuminate\Http\Request
{
    /**
     * @var null
     */
    private static $_instance = null;

    /**
     * Singleton to get request object
     *
     * @return Request
     */
    public static function singleton()
    {
        if (is_null(static::$_instance)) {
            static::$_instance = self::createFromGlobals();
        }

        return static::$_instance;
    }
}