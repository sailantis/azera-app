<?php

namespace App;

use App\Controllers;
use Azera\AppContext;
use Azera\Core\Router;
use Azera\Core\Dispatcher;

class Bootstrap
{
    public function boot(): void
    {
        // Application boot logic
        // e.g., load configuration, initialize services, etc.

        // require(__DIR__ . '/../env.php');

        $ctx        = AppContext::instance();
        $dispatcher = $ctx->getDispatcher();
        $router     = $ctx->getRouter();

        $this->registerMiddleware($dispatcher);
        $this->registerRoutes($router);
    }

    private function registerMiddleware(Dispatcher $dispatcher): void
    {
        // Define middleware groups
        // $dispatcher->defineMiddlewareGroup('protected', [fn() => new \App\Middleware\SiteProtectionMiddleware()]);

        // Set global middleware to start session for all requests
        // $dispatcher->addMiddleware(new \Azera\Http\SessionMiddleware());
    }

    private function registerRoutes(Router $router): void
    {
        // Set controller for the routes
        $router->controller(Controllers\IndexController::class);
        $router->get('/');                       // ::indexAction
        $router->get('/about', '::aboutAction'); // ::aboutAction
    }
}