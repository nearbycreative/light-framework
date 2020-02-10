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
namespace Light;

use Light\Console\Color;
use Psy\Configuration;
use Psy\Shell;

/**
 * Class Console
 *
 * Powers commands for the `light` command line tool.
 *
 * @author Chris Page <chris@nearbycreative.com>
 * @package Light
 */
class Console
{
    /**
     * @var \Twig\Environment|null
     */
    public $twig = null;

    /**
     * List of commands and their descriptions
     * @var array
     */
    public $commands = [
        'help' => 'Displays this help screen',
        'init' => 'Initialize a new Light application',
        'make:model' => [
            'options' => '<modelName>',
            'desc' => 'Create a new model skeleton, usage:  light make:model <ModelName>'
        ],
        'make:controller' => [
            'options' => '<controllerName>',
            'desc' => 'Create a new controller skeleton, usage:  light make:controller <ControllerName>'
        ],
        'make:migration' => [
            'options' => '<migrationName>',
            'desc' => 'Create a new migration skeleton, usage:  light make:migration <migrationName>'
        ],
        'migrate:refresh' => 'Reset and re-run all migrations',
        'migrate:reset' => 'Rollback all database migrations',
        'migrate:rollback' => 'Rollback the last database migration',
        'migrate:status' => 'Show the status of each migration',
        'route:list' => [
            'options' => '--table',
            'desc' => 'List all routes.  Option:  --table'
        ],
        'serve' => [
            'options' => '<host:port>',
            'desc' => 'Boot a PHP server to test your application in the browser, defaults to localhost:8080'
        ],
        'tinker' => 'Interact with your application on the command line',
    ];

    /**
     * Console constructor.
     */
    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
        $this->twig = new \Twig\Environment($loader, [
            //'cache' => '/path/to/compilation_cache',
        ]);
    }

    /**
     * @param string $type ie:  "make:model"
     * @param string $target ie:  "User"
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function make($type, $target)
    {
        $type = strtolower($type);

        $this->ensureFile(getcwd() . '/app/' . ucfirst($type) . '/' . $target . '.php',
            $this->twig->render( $type . '.twig', [
                'appName' => 'App',
                'className' => $target
            ])
        );
    }

    /**
     * @param $dir
     * @param $mode
     * @param bool $recursive
     */
    public function ensureDir($dir, $mode, $recursive = true)
    {
        if (! is_dir($dir)) {
            mkdir($dir, $mode, $recursive);
            echo "Created $dir\n";
        } else {
            echo Color::getColoredString("Skipped mkdir $dir : Directory already exists.\n", 'red');
        }
    }

    /**
     * Create a file with given content.  Will not overwrite existing files.
     *
     * @param $filepath
     * @param $content
     */
    public function ensureFile($filepath, $content)
    {
        if (! file_exists($filepath)) {
            file_put_contents($filepath, $content);
            echo "Created file $filepath\n";
        } else {
            echo Color::getColoredString("Skipped file $filepath : File already exists.\n", 'red');
        }
    }

    /**
     * Create project directories
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function init()
    {
        $cwd = getcwd();

        if (! file_exists('light')) {
            symlink('vendor/bin/light', 'light');
            echo "Created symlink to " . Color::getColoredString("light", 'blue') . " command\n";
        } else {
            echo Color::getColoredString("Skipped light symlink.  File already exists.\n", 'red');
        }

        $directories = [
            'app/Model',
            'app/Controller',
            'config',
            'db/migrations',
            'db/seeds',
            'resources/views',
            'resources/sass',
            'resources/js',
            'public'
        ];

        foreach ($directories as $dir) {
            $this->ensureDir($cwd . '/' . ltrim($dir, '/'), 0744);
        }

        $files = [
            '.env' => $this->twig->render( 'env-example.twig', [
                'APP_KEY' => base64_encode(sha1(time()))
            ]),
            'env-sample' => $this->twig->render( 'env-example.twig', [
                'APP_KEY' => base64_encode(sha1(time()))
            ]),
            'config/routes.php' => $this->twig->render( 'routes.twig'),
            'app/Controller/Welcome.php' => $this->twig->render( 'controller.twig', [
                'appName' => 'App',
                'className' => 'Welcome'
            ]),
            'public/index.php' => $this->twig->render( 'index.twig', [
                'appName' => 'App'
            ]),
            'app/bootstrap.php' => $this->twig->render( 'bootstrap.php.twig', [
                'appName' => 'App'
            ]),
            'phinx.php' => $this->twig->render( 'phinx.php.twig'),
            'public/.htaccess' => $this->twig->render( 'htaccess.twig'),
        ];

        foreach ($files as $filepath => $content) {
            $this->ensureFile($filepath, $content);
        }

        echo Color::getColoredString("Init complete!\n", 'blue');
    }

    /**
     * Show command help
     */
    public function help()
    {
        echo Color::getColoredString("Light Framework by Nearby Creative (https://nearbycreative.com)\n\n", 'green');
        echo "Usage:\n";
        echo Color::getColoredString("\tcommand [options] [arguments]\n\n", 'yellow');
        echo "Available commands:\n\n";
        foreach ($this->commands as $command => $description) {
            if (is_array($description)) {
                echo "\t"
                    . Color::getColoredString(str_pad($command, 25), 'green')
                    . Color::getColoredString(str_pad($description['options'], 25), 'yellow')
                    . $description['desc'] . "\n";
            } elseif(is_string($description)) {
                echo "\t"
                    . Color::getColoredString(str_pad($command, 25), 'green')
                    . Color::getColoredString(str_pad('', 25), 'yellow')
                    . $description . "\n";
            } else {
                echo "No description";
            }
        }
        echo "\n";
    }

    /**
     * Run command line commands
     *
     * @param $argv
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run($argv)
    {
        $command = $argv[1] ?? null;

        if (in_array($command, ['make:model', 'make:controller'])) {
            list($command, $type) = explode(':', $command);

            $target = $argv[2] ?? null;

            if ($target) {
                $this->make($type, $target);
            }
        } elseif ($command === 'init') {
            $this->init();
        } elseif ($command === 'serve') {
            $this->serve($argv[2] ?? null);
        } elseif ($command === 'tinker') {
            $this->tinker();
        } elseif($command === 'route:list') {
            $this->list_routes($argv);
        } elseif($command === 'make:migration') {
            $this->make_migration($argv);
        } else {
            $this->help();
        }
    }

    /**
     * Launch a php server to test the application
     *
     * @param string $host Hostname, ie:  'localhost' or 'localhost:8080'.  Defaults to 'localhost:8080'
     */
    public function serve($host)
    {
        if (is_null($host)) {
            $host = 'localhost:8080';
        }

        echo Color::getColoredString("Started Light server on $host\n", 'blue');
        $serverCmd = "php -S $host -t public public/index.php";
        echo exec("cd " . getcwd() . " && $serverCmd");
    }

    /**
     * Adds controller routes to the route map for display in `php light route:list`
     *
     * Only public methods are scanned in auto controller routing and unlike
     * config/routes.php, controller routes map to all HTTP verbs at this time.
     *
     * Perhaps a future version will introduce get_[action], post_[action] to controllers.
     *
     * @see Console::list_routes()
     * @param App $app An instance of Light App
     * @return mixed
     */
    public function addControllerRoutes(App $app)
    {
        //Bootstrap the app's project directory
        $loader = require_once 'vendor/autoload.php';
        $loader->addPsr4('App\\', 'app/');

        //Glob all the controllers
        $controllers = \rglob('app/Controller/*.php');
        foreach ($controllers as $controller) {
            $classPath = str_replace(
                '.php',
                '',
                        str_replace(
                            '/',
                            '\\',
                                    ucwords($controller, '/')
                        )
            );

            $instance = new $classPath();
            $methods = get_class_methods($instance); //Only grabs public methods

            foreach ($methods as $method) {
                //Ignore magic methods
                if (! preg_match('/^__\.*/', $method)) {
                    $uri = str_replace(
                        'app/controller', '',
                        str_replace(
                            '.php',
                            '',
                            strtolower($controller)) . '/' . $method
                    );

                    $app->routes->any($uri, $classPath . '@' . $method);
                }
            }
        }

        return $app->routes->map;
    }

    /**
     * List all routes available
     *
     * This includes both routes configured by config/routes.php as well as all the
     * public methods in app/controllers
     *
     * @see Console::addControllerRoutes()
     */
    public function list_routes($argv)
    {
        require_once 'Global.php';

        $routeFile = 'config/routes.php';
        $app = new \Light\App($routeFile); //This call maps config/routes.php

        if (isset($app->routes) && isset($app->routes->map) && is_array($app->routes->map) && count($app->routes->map)) {

            $map = $this->addControllerRoutes($app); //Controller routes are added

            ksort($map); //Sort routes by URI


            if (isset($argv[2]) && $argv[2] === '--table') {
                //Display a pretty table of the route maps
                foreach ($map as $route => $methods) {
                    echo "\n" . Color::getColoredString($route, 'blue') . "\n";
                    echo '+' . str_pad('', '71', '-') . "+\n";
                    echo "| \t" . Color::getColoredString(str_pad('METHOD', 10, ' '), 'purple') . ' | ' . Color::getColoredString(str_pad('ACTION', '50', ' '), 'purple') . " |\n";

                    foreach ($methods as $method => $handler) {
                        if ($handler instanceof \Closure) {
                            $handler_name = 'Route Closure, see ' . $routeFile;
                        } else {
                            $handler_name = $handler;
                        }
                        echo '+' . str_pad('', '71', '-') . "+\n";
                        echo "| \t" . Color::getColoredString(str_pad($method, 10, ' '), 'yellow') . ' | ' . str_pad($handler_name, '50', ' ') . " |\n";
                    }
                    echo '+' . str_pad('', '71', '-') . "+\n";
                }
            } else {
                foreach ($map as $route => $methods) {
                    echo "\n" . Color::getColoredString($route, 'blue') . "\n";
                    echo "\t" . Color::getColoredString(str_pad('METHOD', 10, ' '), 'purple') . Color::getColoredString(str_pad('ACTION', '50', ' '), 'purple') . "\n";

                    foreach ($methods as $method => $handler) {
                        if ($handler instanceof \Closure) {
                            $handler_name = 'Route Closure, see ' . $routeFile;
                        } else {
                            $handler_name = $handler;
                        }

                        echo "\t" . Color::getColoredString(str_pad($method, 10, ' '), 'yellow') . str_pad($handler_name, '50', ' ') . "\n";
                    }

                }
            }
        }
    }

    /**
     * Helper wrapper to the phinx command to generate a migration
     */
    public function make_migration($argv)
    {
        echo exec("./vendor/bin/phinx create " . $argv[2]) . "\n";
    }

    /**
     * Provides an interactive shell for tinkering with an app
     *
     * <code>
     * php light tinker
     *
     * light> $app = \Light\App::singleton()
     * light> $app->run('get', '/user/1')
     * </code>
     */
    public function tinker()
    {
        $config = new Configuration([
            'updateCheck' => 'never',
            'usePcntl' => false,
            'prompt' => Color::getColoredString('light> ', 'blue'),
            'startupMessage' => sprintf('<info>%s</info>', "Tinker with your app interactively!\nTry:\n\t\$app = Light\App::singleton()\n\t\$app->run('get','/')\n\n"),
            'defaultIncludes' => [
                'app/bootstrap.php'
            ]
        ]);

        $shell = new Shell($config);
        $shell->run();
    }
}