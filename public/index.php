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
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Light\App();
$route = $app->routes;

$route->get('/control', Light\Controller\Test::class . '@index');

$route->group('/admin', function() use ($route) {
    $route->get('/profile', function() {
        return ['admin/profile'];
    });

    $route->group('/manage', function() use ($route) {
        $route->get('/users', function() {
            return ['admin/manage/users'];
        });

        $route->get('/comments', function() {
            return ['admin/manage/comments'];
        });
    });
});

$route->map(['POST', 'PUT', 'GET'], '/multiple', function() {
    return [
        'method' => request()->method(),
        'message' => 'Made a request!'
    ];
});

$route->get('/somewhere', function () {
    return ['here'];
});

$route->redirect('/go','/admin/manage/users');
/**
 * Base route
 */
$route->get('/', function () {
   return ['foo' => 'bar'];
});

$route->post('/location/add', function () {

});

/**
 * Example of named URL parameters
 */
$route->get('/test/{id:\d+}/{test:\d+}', function ($id, $test) {

    return [
        'id' => $id,
        'test' => $test
    ];
});

/**
 * Examaple of using a slug
 */
$route->get('/articles/{id:\d+}', function ($id) {

    return [
        'id' => $id,
    ];
});

/**
 * Examaple of using an optional segment
 */
$route->get('/articles/{category}[/{title}]', function ($category, $title) {

    return [
        'category' => $category,
        'title' => $title ?? null,
    ];
});


$app->run();

