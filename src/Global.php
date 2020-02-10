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

/**
 * Include these methods in the global namespace for convenience
 *
 * @author Chris Page <chris@nearbycreative.com>
 * @package Light
 */
namespace {

    use Light\Request;
    use Light\App;

    /**
     * Get the request object
     *
     * @return Request
     */
    function request()
    {
        return Request::singleton();
    }

    /**
     * @return \Light\RouteCollector|null
     */
    function route()
    {
        return App::singleton()->routes;

    }

    /**
     * Recursive glob
     *
     * @param $pattern
     * @param int $flags
     * @return array|false
     */
    function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, \rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}