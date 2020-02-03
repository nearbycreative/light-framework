# Embed into an existing application

Instructions as soon as I have time to write it up.  Some starting points:

### Create a dispatcher that uses Light

```
namespace YourApp;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Light\App();
$route = $app->routes;

$route->get('/foo', Test::class . '@index');
```

### .htaccess


Light Framework uses mod_rewrite to route URLs to the correct actions.  
Add this to your _.htaccess_ to route urls your app entry point.

Here is an example using an Apache directive.  You'll have to modify this to route requests 
to the dispatcher you created. 

```
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
  RewriteRule ^(.*) - [E=BASE:%1]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^ index.php [QSA,L]
</IfModule>
```


Spin up a test server:
    
    php -S localhost:8080 -t public public/index.php