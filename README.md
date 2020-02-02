# Quickstart

    composer require nearbycreative/light-framework

#### Example Directory structure

For purpose of example we'll assume your application directory structure looks like this:

    app/
        controller/
        model/
    public/
        .htaccess
        index.php
    composer.json
    

Note, you can use any directory structure you wish.

#### composer.json and .htaccess

In order for the following examples to work, you'll need your composer.json to be able to
autoload your files.  Make sure you have a PSR-4 autoload entry in your composer.json

If you're working from scratch you can use this one:

```
{
    "name": "yourcompany/name",
    "autoload": {
        "psr-4": {
            "YourApp\\": "app/"
        }
    },
    "require": {
        "ext-json": "*",
        "php": ">=7.2",
        "nearbycreative/light-framework": "*"
    }
}
```

Light Framework uses mod_rewrite to route URLs to the correct actions.  
Add this to your _.htaccess_ to route urls your app entry point.  

```
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
  RewriteRule ^(.*) - [E=BASE:%1]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^ index.php [QSA,L]
</IfModule>
```

### Implement using closures:

Light Framework is a micro-framework for spinning up API's quickly.  If you wanted to, you could create your entire application in a single file using closures:

Example index.php:

```
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Light\App();
$route = $app->routes;

$route->get('/test', function() {
    return ['hello' => 'world'];
});

$route->get('/test/{slug}', function($slug) {
    return ['slug' => $slug];
});
```

    
Spin up a test server:
    
    php -S localhost:8080 -t public public/index.php
    
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

Create a controller in your application that extends Light\Controller

```
namespace YourApp;

class Test extends \Light\Controller
{
    /**
     * A test action
     *
     * @return array
     */
    public function index()
    {
        return ['bar'];
    }
}
```

Create a dispatch file.  Ie, index.php:

```
namespace YourApp;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Light\App();
$route = $app->routes;

$route->get('/foo', Test::class . '@index');
```

Spin up a test server:
    
    php -S localhost:8080 -t public public/index.php
    
Visit: http://localhost:8080/foo


