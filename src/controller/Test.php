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
namespace Light\Controller;

/**
 * Class Test
 *
 * An example controller.  You can create a file called index.php and put this in it:
 *
 * <code>
 *     require_once __DIR__ . '/../vendor/autoload.php';
 *
 *     $app = new Light\App();
 *     $route = $app->routes;
 *
 *     $route->get('/test', Light\Controller\Test::class . '@index');
 * </code>
 *
 * Spin up a server:  php -S localhost:8080 -t public public/index.php
 *
 *
 * Then visit http://localhost/test
 *
 * @package Light\Controller
 */
class Test extends \Light\Controller
{
    /**
     * A test action
     *
     * @return array
     */
    public function index()
    {
        return ['foo' => 'bar'];
    }
}

