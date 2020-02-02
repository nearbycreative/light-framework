<?php


/**
 * Include these methods in the global namespace for convenience
 */
namespace {

    use Illuminate\Http\Request;

    /**
     * Get the request object
     *
     * @return \Illuminate\Http\Request
     */
    function request()
    {
        return Request::createFromGlobals();
    }
}