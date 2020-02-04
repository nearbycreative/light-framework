# Light Command Line Utility

The `php light` command line utility is available in the root directory of a Light project and is helpful for performing 
common application tasks such as scaffolding new models and controllers, managing migrations, and 
booting the built in PHP web server.

#### Available commands:  


Command | Description | Example 
: --- :| --- | ---
help | Displays this help screen| `light help`
init |Initialize a new Light application| `light init`  
make:model | Create a new model skeleton| `light make:model Comment`
make:controller |Create a new controller skeleton|`light make:controller Comment`
make:migration |Create a new migration skeleton|`light make:migration AddCommentsTable`
migrate:refresh |Reset and re-run all migrations|
migrate:rollback |Rollback all database migrations|
migrate:status |Show the status of each migration|
route:list | List all registered routes|
serve |Boot a PHP server to test your application in the browser| `light serve` or `light serve localhost:8080`
tinker |Interact with your application|

