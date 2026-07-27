# Azera App

A starter project for the [Azera PHP Framework](https://github.com/sailantis/azera-framework).

## Quick Start

### 1. Create a New Project

```bash
composer create-project sailantis/azera-app my-app
cd my-app
```

### 2. Configure Environment

Copy the example environment file and configure your settings:

```bash
cp env.example.php env.php
```

Edit `env.php` with your database credentials and application settings.

### 3. Run the Development Server

```bash
composer serve
```

Or use the Azera CLI directly:

```bash
php vendor/bin/azera serve
```

The application will be available at `http://localhost:8080`.

### 4. Explore Available Commands

```bash
# List all routes
php vendor/bin/azera routes

# Check application status
php vendor/bin/azera about

# Database operations
php vendor/bin/azera db
```

## Project Structure

```
azera-app/
├── app/
│   ├── Bootstrap.php          # Application bootstrap
│   ├── AppContext.php          # Application context
│   └── Controllers/           # Your controllers
│       └── IndexController.php
├── public/
│   └── index.php              # Entry point
├── views/                     # View templates
│   ├── home.php
│   └── about.php
├── env.example.php            # Environment example
└── composer.json
```

## Adding Routes

Edit `app/Bootstrap.php` and add routes in the `registerRoutes()` method:

```php
public function registerRoutes(Router $router): void
{
    $router->get('/', [Controllers\IndexController::class, 'index']);
    $router->get('/about', [Controllers\IndexController::class, 'about']);
    
    // Add your routes here
    $router->get('/hello/{name}', [Controllers\IndexController::class, 'hello']);
}
```

## Creating Controllers

Create a new controller in `app/Controllers/`:

```php
<?php

namespace App\Controllers;

use Core\Http\Request;
use Core\Http\Response;

class HelloController
{
    public function greet(Request $request): Response
    {
        $name = $request->param('name');
        return Response::view('hello.php', [
            'title' => 'Hello ' . $name,
            'name' => $name
        ]);
    }
}
```

## Database Setup

1. Configure your database in `env.php`
2. Run migrations (if available):
   ```bash
   php vendor/bin/azera db:migrate
   ```
3. Or create tables directly:
   ```bash
   php vendor/bin/azera db:tables
   ```

## Testing

```bash
./vendor/bin/phpunit
```

## License

MIT

## Resources

- [Azera Framework Documentation](https://github.com/sailantis/azera-framework)
- [Issue Tracker](https://github.com/sailantis/azera-app/issues)