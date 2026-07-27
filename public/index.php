<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\AppContext;
use App\Bootstrap;

$ctx = new AppContext();
$ctx->boot(new Bootstrap());

$request  = \Core\Http\Request::fromGlobals();
$response = $ctx->handle($request);
$response->send();