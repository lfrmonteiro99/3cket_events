<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exception\RouteNotFoundException;
use App\Router\HttpMethod;
use App\Router\Route;
use App\Router\Router;
use App\Service\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /** @var Container&MockObject */
    private MockObject $container;
    private Router $router;

    public function testAddRoute(): void
    {
        $this->router->addRoute(HttpMethod::GET, '/test', 'TestController', 'index');

        $route = $this->router->resolve('GET', '/test');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals(HttpMethod::GET, $route->getMethod());
        $this->assertEquals('/test', $route->getPath());
        $this->assertEquals('TestController', $route->getController());
        $this->assertEquals('index', $route->getAction());
    }

    public function testGetMethod(): void
    {
        $this->router->get('/test', 'TestController', 'index');

        $route = $this->router->resolve('GET', '/test');

        $this->assertEquals(HttpMethod::GET, $route->getMethod());
    }

    public function testRouteNotFound(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $this->router->resolve('GET', '/nonexistent');
    }

    public function testParameterizedRoute(): void
    {
        $this->router->addRoute(HttpMethod::GET, '/events/{id}', 'EventController', 'show');

        $route = $this->router->resolve('GET', '/events/123');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('/events/{id}', $route->getPath());
        $this->assertEquals('EventController', $route->getController());
        $this->assertEquals('show', $route->getAction());
    }

    public function testExtractParameters(): void
    {
        $route = new Route(HttpMethod::GET, '/events/{id}', 'EventController', 'show');

        $parameters = $route->extractParameters('/events/123');

        $this->assertEquals(['id' => '123'], $parameters);
    }

    public function testExtractMultipleParameters(): void
    {
        $route = new Route(HttpMethod::GET, '/events/{id}/comments/{commentId}', 'EventController', 'showComment');

        $parameters = $route->extractParameters('/events/123/comments/456');

        $this->assertEquals(['id' => '123', 'commentId' => '456'], $parameters);
    }

    public function testParameterizedRouteWithNoParameters(): void
    {
        $route = new Route(HttpMethod::GET, '/events', 'EventController', 'index');

        $parameters = $route->extractParameters('/events');

        $this->assertEquals([], $parameters);
    }

    public function testParameterizedRouteMatching(): void
    {
        $route = new Route(HttpMethod::GET, '/events/{id}', 'EventController', 'show');

        $this->assertTrue($route->matches(HttpMethod::GET, '/events/123'));
        $this->assertTrue($route->matches(HttpMethod::GET, '/events/abc'));
        $this->assertFalse($route->matches(HttpMethod::GET, '/events/123/extra'));
        $this->assertFalse($route->matches(HttpMethod::POST, '/events/123'));
    }

    protected function setUp(): void
    {
        // @var Container|MockObject $container
        $this->container = $this->createMock(Container::class);
        $this->router = new Router($this->container);
    }
}
