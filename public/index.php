<?php
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

