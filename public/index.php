<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Azera\AppContext;
use Azera\Http\Response;

(new Bootstrap)->boot();

$ctx     = AppContext::instance();
$request = $ctx->request();

// Get the current request URI and method
$path   = $ctx->request()->getPath();
$method = $ctx->request()->getMethod();

// Match the route and dispatch
$route = $ctx->router()->match($path, $method);
if ($route === null) {
    // No route matched
    $response = Response::status(404);
} else {
    // Dispatcher will invoke the controller action and store enriched route info in AppContext
    $response = $ctx->dispatcher()->dispatch($route);
}
// Send the response to the client
$response->send();