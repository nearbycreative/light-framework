<?php

namespace Light;

/**
 * Class Config
 *
 * There are multiple configurations in the config directory, and arbitrary configs
 * can be added simply by adding new files.
 *
 * The structure is as follows:
 *
 * config/[configName].php
 *
 * <code>
 * $appConfig = \Nearby\Config::get()['host']
 * </code>
 *
 * @package Light
 */
class Config
{
    /**
     * Singleton pattern for all loaded configs
     *
     * @var array
     */
    public static $_configs = [];

    /**
     * Return the configuration specified by $name.
     *
     * @param string $name
     * @return array
     */
    public static function get($name, $key)
    {
        if (! isset(self::$_configs[$name])) {
            if (file_exists('../config/' . $name . '.php')) {
                self::$_configs[$name] = require '../config/' . $name . '.php';
            } else {
                self::$_configs[$name] = require 'config/' . $name . '.php';
            }
        }

        return self::$_configs[$name][$key] ?? null;
    }

    /**
     * @param $key
     */
    public static function env($key)
    {

    }
}