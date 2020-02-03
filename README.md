Light is a micro-framework for quickly spinning up powerful APIs.  It can 
be used both as a standalone framework or embedded into an existing
application.

Frameworks like Laravel and Symfony are powerful, but when you need to build out an 
API, a web framework with the whole kitchen sink can over complicate with option overload.

Light aims to use familiar open source technologies like Eloquent and Phinx, with simple paradigms like MVC
to make building out APIs lightning fast. 

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
    
    
That's it!  You can visit http://localhost:8080 to see Light in action!
 
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

If you look at the generated _public/index.php_, you'll see it provided a few simple routes to start you off.  If you 
wanted to, you could create your entire application in a single file using just route closures, ie:

An example _index.php_:

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

### Using controllers

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

## Command Line Utility

Try running `php light help` to learn about more things you can do with the command line script.


# Learning more:

The following docs are coming soon!

* [Routing](docs/routing.md)
* [Requests](docs/requests.md)
* [Controllers](docs/controllers.md)
* [Models](docs/models.md)
* [Migrations](docs/migrations.md)
* [Pagination](docs/pagination.md)
* [Helpers](docs/helpers.md)
* [Authentication](docs/authentication.md)
* [Light Command Line Utility](docs/light_command.md)
* [Embedding Light into existing projects](docs/embed.md)
* [Using Light as a full MVC Web Framework too](docs/views.md)


