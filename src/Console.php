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
        'make:model' => 'Create a new model skeleton, usage:  light make:model <ModelName>',
        'make:controller' => 'Create a new controller skeleton, usage:  light make:controller <ControllerName>',
        'make:migration' => 'Create a new migration',
        'migrate:refresh' => 'Reset and re-run all migrations',
        'migrate:reset' => 'Rollback all database migrations',
        'migrate:rollback' => 'Rollback the last database migration',
        'migrate:status' => 'Show the status of each migration',
        'route:list' => 'List all registered routes',
        'serve' => 'Boot a PHP server to test your application in the browser',
        'tinker' => 'Interact with your application',
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
            'database/migrations',
            'database/seeds',
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
            'app/Controller/Welcome.php' => $this->twig->render( 'controller.twig', [
                'appName' => 'App',
                'className' => 'Welcome'
            ]),
            'public/index.php' => $this->twig->render( 'index.twig', [
                'appName' => 'App'
            ]),
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
        echo "\tcommand [options] [arguments]\n\n";
        echo "Available commands:\n\n";
        foreach ($this->commands as $command => $description) {
            echo "\t"
                . Color::getColoredString(str_pad($command, 25), 'green')
                . $description . "\n";
        }
        echo "\n";
    }

    /**
     * Run command line commands
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
        } elseif (in_array($command, ['init'])) {
            $this->init();
        } elseif (in_array($command, ['serve'])) {
            $host = $argv[2] ?? 'localhost:8080';

            echo Color::getColoredString("Started Light server on $host\n", 'blue');
            $serverCmd = "php -S $host -t public public/index.php";
            echo exec("cd " . getcwd() . " && $serverCmd");

        } else {
            $this->help();
        }
    }
}