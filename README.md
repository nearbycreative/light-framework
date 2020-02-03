Light is a micro-framework for quickly spinning up APIs.  It can 
be used both as a standalone framework or embedded into an existing
application.

# Requirements

* php7.2+
* A web server with `mod_rewrite` enabled

# Quickstart a Fresh Project

If you're starting your project from scratch, the following commands will get you up and 
running quickly!

    mkdir myproject && cd myproject
    
    composer require nearbycreative/light-framework
    
    ./vendor/bin/light init   
    php light serve
    
    
From here, you can visit http://localhost:8080 to see Light in action!
 
#### Directory structure

`light init` creates the following directory structure: 

    app/
        controller/
            Welcome.php
        model/
    database/
        migrations/
        seeds/
    public/
        .htaccess
        index.php
    resources/
        js/
        sass/
        views/
    .env
    light
    
    
Note, this is the default scaffolding to get a new application up and running quickly.  If you're including Light
into an existing project, you probably don't want to run `light init`, but rather embed it.  [Learn how to embed Light in an existing application](docs/embed.md).  


### Using route closures:

Light Framework is a micro-framework for spinning up API's quickly.  If you wanted to, you could create your entire 
application in a single file using closures:

Example index.php:

```
require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Light\App();
$route = $app->routes;

$route->get('/test', function() {
    return ['hello' => 'world'];
});

$route->get('/test/{slug}', function($slug) {
    return ['slug' => $slug];
});
```

    
Spin up a test server with `php light serve` command.
    
Visit: http://localhost:8080/test

You'll see the following output:

```
{
    "code": 200,
    "status": "success",
    "time": "2020-02-02T05:09:15.457290Z",
    "data": {
        "hello": "world"
    }
}
```

Visit: http://localhost:8080/test/this-is-cool

You'll see the following output:

```
{
    "code": 200,
    "status": "success",
    "time": "2020-02-02T05:09:15.457290Z",
    "data": {
        "slug": "this-is-cool"
    }
}
```

### Implementation using controllers

Putting all your API endpoints in route closures works fine for small APIs.  Though, this can get 
pretty narly, pretty quickly.  Another way to organize your API code is using _Light Controllers._
  
You can create a controller in your application that extends Light\Controller manually, or use the `php light make:controller` command.

Ie:  `php light make:controller Test` will generate the following controller in _app/Controller/Test.php:

```
<?php

namespace App\Controller;

/**
 * Test Controller
 *
 * @packaage App\Controller
 */
class Test extends \Light\Controller
{

    /**
     * Index
     */
    public function index()
    {
        return ['hello' => 'world'];
    }

}
```

Inside _index.php_ add the following route that points to the controller action you just created:

```
$route->get('/foo', Test::class . '@index');
```

Use `php light serve` to test out your new API endpoint:
    

Visit: http://localhost:8080/foo


