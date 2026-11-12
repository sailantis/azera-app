<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Azera\AppContext;
use Azera\Http\Response;

(new Bootstrap())->boot();

$ctx     = AppContext::instance();
$request = $ctx->request();

// Match the route and dispatch. The dispatcher invokes the controller action
// and stores the enriched route info in AppContext.
$route = $ctx->router()->match($request->path(), $request->method());

$response = $route === null
    ? Response::text("404 Not Found\n", 404)
    : $ctx->dispatcher()->dispatch($route);

// Send the response to the client
$response->send();