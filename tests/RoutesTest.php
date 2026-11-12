<?php

namespace Tests;

use App\Bootstrap;
use Azera\AppContext;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase
{
    protected function setUp(): void
    {
        AppContext::reset();
        (new Bootstrap())->boot();
    }

    protected function tearDown(): void
    {
        AppContext::reset();
    }

    public static function knownRoutes(): array
    {
        return [
            'home'  => ['/', 'Welcome to Azera Framework'],
            'about' => ['/about', 'About - Azera Framework'],
        ];
    }

    /**
     * @dataProvider knownRoutes
     */
    public function testRouteRendersItsView(string $path, string $title): void
    {
        $ctx   = AppContext::instance();
        $route = $ctx->router()->match($path, 'GET');

        $this->assertNotNull($route, "No route matched GET {$path}");

        $response = $ctx->dispatcher()->dispatch($route);

        $this->assertSame(200, $response->getStatus());
        $this->assertStringContainsString("<title>{$title}</title>", $response->getBody());
    }

    public function testUnknownRouteDoesNotMatch(): void
    {
        $this->assertNull(AppContext::instance()->router()->match('/missing', 'GET'));
    }
}
