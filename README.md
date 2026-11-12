# Azera App

A starter application for the [Azera PHP Framework](https://github.com/sailantis/azera-framework).
It includes MVC routing, Clarity templates, PSR-4 autoloading and a console.

## Requirements

- PHP >= 8.2
- Composer
- `ext-pdo` and `ext-mbstring`

## Getting Started

### 1. Clone and Install

```bash
git clone https://github.com/sailantis/azera-app my-app
cd my-app
composer install
```

### 2. Configure the Environment (optional)

The application can start without this file. Database tasks require it. Create
`env.php` from the example:

```bash
cp env.example.php env.php
```

When `env.php` exists, `boot()` loads it and registers a lazy `default` database
connection using the `DB_*` constants. The connection is opened when a query runs.

### 3. Run the Development Server

```bash
composer serve
```

Or use the Azera CLI directly:

```bash
php vendor/bin/azera serve
```

Both commands start the server at `http://localhost:8000` by default. Pass
`--port=` to use a different port:

```bash
php vendor/bin/azera serve --port=8080
```

### 4. Use the CLI

```bash
# List registered routes
php vendor/bin/azera routes:list

# Show the framework version, resolved bootstrap and view engine
php vendor/bin/azera about

# List available tasks and actions
php vendor/bin/azera help
```

The `composer routes` and `composer db` scripts run `routes:list` and `db:tables`,
respectively.

## Project Structure

```text
azera-app/
├── app/
│   ├── Bootstrap.php           # BootstrapProvider: views, database, routes
│   └── Controllers/            # Your controllers
│       └── IndexController.php
├── public/
│   └── index.php               # Entry point
├── tests/
│   └── RoutesTest.php          # Smoke tests for the shipped routes
├── views/                      # Clarity templates
│   ├── home.clarity.html
│   └── about.clarity.html
├── env.example.php             # Environment example
├── phpunit.xml
└── composer.json
```

## Routing

[app/Bootstrap.php](app/Bootstrap.php) implements
`Azera\Boot\BootstrapProvider`, which allows the `azera` CLI to discover and
start the application. Add routes in `registerRoutes()`:

```php
private function registerRoutes(Router $router): void
{
    $router->controller(Controllers\IndexController::class, function (Router $r): void {
        $r->get('/', '::indexAction');
        $r->get('/about', '::aboutAction');
        $r->get('/hello/{name}', '::helloAction');
    });
}
```

A handler must be a **string**. Supported forms include `'Controller::action'`,
`::action` inside a `controller()` block, and an array of strings such as
`['controller' => IndexController::class, 'action' => 'indexAction']`. Closures
and objects are not supported because the route table must be serializable for
the route cache.

Path variables are passed to the action as parameters with matching names:

```php
public function helloAction(string $name): string
{
    return 'Hello ' . $name;
}
```

### Enabling the route cache

Wrap the route declarations in `cached()`. This enables caching and identifies
the files used to check whether the route table is current:

```php
$router->cached(__FILE__, function (Router $r): void {
    // ...routes...
});
```

The first request that reads the router builds the table and writes it to a
derived temporary path. Later requests load the cached table with `require`.
`routes:status` reports whether the cache is current, `routes:cache` rebuilds it,
and `routes:clear` removes it.

## Controllers

Create a controller in `app/Controllers/` that extends
`Azera\Core\Controller`:

```php
<?php

namespace App\Controllers;

use Azera\Http\Request;
use Azera\Http\Response;

class HelloController extends \Azera\Core\Controller
{
    public function greetAction(Request $request, string $name): Response
    {
        return Response::json([
            'title' => 'Hello ' . $name,
            'secure' => $request->isSecure(),
        ]);
    }
}
```

The dispatcher converts action return values as follows: a `Response` is sent
unchanged, a `string` becomes an HTML response, an `array` becomes JSON, an
`int` is used as the status code, and `null` becomes status `204`. The container
injects parameters typed as `Request`.

## Views

The starter renders Clarity templates from `views/`. `configureViews()`
registers a lazy factory for the view engine, so booting the application does
not create the engine:

```php
private function configureViews(AppContext $ctx): void
{
    $viewsPath = \dirname(__DIR__) . '/views';

    $ctx->set(
        ViewEngine::class,
        fn() => (new ClarityEngine())->setViewPath($viewsPath),
    );
}
```

Render a template from a controller with
`$this->view()->render('home', ['title' => 'Welcome'])`. The `.clarity.html`
extension is implied. Run `php vendor/bin/azera make:view users.index` to create
`views/users/index.clarity.html`.

## Database

Define the `DB_*` constants in `env.php`. `boot()` registers a factory for the
`default` database role, so its PDO connection opens on first use:

```php
$db = \Azera\AppContext::instance()->dbManager()->get('default');
$rows = $db->query('SELECT * FROM users')->fetchAll();
```

Useful tasks:

```bash
php vendor/bin/azera db:tables              # List tables
php vendor/bin/azera db:table <name>        # Show a table's columns
php vendor/bin/azera db:query "SELECT 1"    # Run raw SQL
php vendor/bin/azera migrate:diff           # Model definitions vs. live schema
php vendor/bin/azera model:sync <Model>     # Sync a model from its table
```

Add roles with `dbManager()->set('analytics', fn() => new Database(...))`, then
retrieve them with `get('analytics')`.

## Testing

```bash
composer test
```

PHPUnit reads [phpunit.xml](phpunit.xml) and runs the tests in `tests/`.
[tests/RoutesTest.php](tests/RoutesTest.php) starts the application, matches
each included route and checks the rendered page. Run `php
vendor/bin/azera test` to execute the same suite through the CLI.

## License

MIT

## Resources

- [Azera Framework Documentation](https://github.com/sailantis/azera-framework/tree/main/docs)
- [Framework CHANGELOG](https://github.com/sailantis/azera-framework/blob/main/CHANGELOG.md)
- [Issue Tracker](https://github.com/sailantis/azera-app/issues)
