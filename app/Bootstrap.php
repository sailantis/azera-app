<?php

namespace App;

use App\Controllers;
use Azera\AppContext;
use Azera\Boot\BootstrapProvider;
use Azera\Core\Dispatcher;
use Azera\Core\Engines\ClarityEngine;
use Azera\Core\Router;
use Azera\Core\ViewEngine;
use Azera\Db\Database;

/**
 * Application bootstrap.
 *
 * Implementing Azera\Boot\BootstrapProvider is what lets the `azera` CLI find
 * this class and boot the application for tasks such as `routes:list`.
 */
class Bootstrap implements BootstrapProvider
{
    public function boot(): void
    {
        $envFile = \dirname(__DIR__) . '/env.php';
        if (is_file($envFile)) {
            require_once $envFile;
        }

        $ctx = AppContext::instance();

        $this->configureDatabase($ctx);
        $this->configureViews($ctx);
        $this->registerMiddleware($ctx->dispatcher());
        $this->registerRoutes($ctx->router());
    }

    private function configureDatabase(AppContext $ctx): void
    {
        if (!\defined('DB_DRIVER')) {
            return;
        }

        $dsn = match (DB_DRIVER) {
            'sqlite' => 'sqlite:' . DB_NAME,
            'pgsql'  => \sprintf('pgsql:host=%s;port=%d;dbname=%s', DB_HOST, DB_PORT, DB_NAME),
            default  => \sprintf('mysql:host=%s;port=%d;dbname=%s', DB_HOST, DB_PORT, DB_NAME),
        };

        // A factory, so the connection is only opened by the code that needs it.
        $ctx->dbManager()->set(
            'default',
            fn() => new Database($dsn, DB_USER, DB_PASS),
        );
    }

    private function configureViews(AppContext $ctx): void
    {
        // Deferred factory: boot must not build the engine, or the Clarity
        // class stack is autoloaded on every request instead of on first render.
        $viewsPath = \dirname(__DIR__) . '/views';

        $ctx->set(
            ViewEngine::class,
            fn() => (new ClarityEngine())->setViewPath($viewsPath),
        );
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
        $router->controller(Controllers\IndexController::class, function (Router $r): void {
            $r->get('/', '::indexAction');
            $r->get('/about', '::aboutAction');
        });
    }
}
