<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest\Authentication;

use Aura\Session\Segment;
use Aura\Session\Session;
use DaMess\Http\SessionMiddleware;
use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\SetCookies;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Mockery as M;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stickee\Auth\Authentication\Middleware;
use Stickee\Auth\Authentication\Service;
use Stickee\AuthTest\FakeJWTTrait;
use Stickee\AuthTest\MockeryTrait;
use Zend\Diactoros\Response\RedirectResponse;

class MiddlewareTest extends TestCase
{
    use FakeJWTTrait;
    use MockeryTrait;

    /**
     * @var Service|\Mockery\Mock $authentication_service
     */
    private $authentication_service;

    /**
     * @var \Faker\Generator $faker
     */
    private $faker;

    /**
     * @var Middleware $middleware
     */
    private $middleware;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->faker = \Faker\Factory::create();
        $this->authentication_service = M::mock(Service::class);
        $this->middleware = new Middleware($this->authentication_service);
    }

    public function testProcess()
    {
        /** @var ServerRequestInterface|\Mockery\Mock $request */
        $request = M::mock(ServerRequestInterface::class);

        /** @var DelegateInterface|\Mockery\Mock $delegate */
        $delegate = M::mock(DelegateInterface::class);

        /** @var Session|\Mockery\Mock $session */
        $session = M::mock(Session::class);

        /** @var Segment|\Mockery\Mock $segment */
        $segment = M::mock(Segment::class);

        $request->shouldReceive('getAttribute')->once()->with(SessionMiddleware::KEY)->andReturn($session);
        $session->shouldReceive('getSegment')->once()->andReturn($segment);

        $fake_jwt = $this->generateFakeJWT([]);
        $segment->shouldReceive('get')->once()->with('id_token')->andReturn($fake_jwt);

        $this->authentication_service->shouldReceive('idTokenIsValid')->once()->with($fake_jwt)->andReturn(true);

        /** @var ResponseInterface|\Mockery\Mock $response */
        $response = M::mock(ResponseInterface::class);

        $response->shouldReceive('getHeader')->once()->with(SetCookies::SET_COOKIE_HEADER)->andReturn([]);
        $response->shouldReceive('withoutHeader')->once()->with(SetCookies::SET_COOKIE_HEADER)->andReturnSelf();
        $response->shouldReceive('withAddedHeader')->once()
            ->with(SetCookies::SET_COOKIE_HEADER, M::type('string'))
            ->andReturnSelf();

        $delegate->shouldReceive('process')->once()->with($request)->andReturn($response);

        $this->middleware->process($request, $delegate);
    }

    public function testProcessNoToken()
    {
        /** @var ServerRequestInterface|\Mockery\Mock $request */
        $request = M::mock(ServerRequestInterface::class);

        /** @var DelegateInterface|\Mockery\Mock $delegate */
        $delegate = M::mock(DelegateInterface::class);

        /** @var Session|\Mockery\Mock $session */
        $session = M::mock(Session::class);

        /** @var Segment|\Mockery\Mock $segment */
        $segment = M::mock(Segment::class);

        $request->shouldReceive('getAttribute')->once()->with(SessionMiddleware::KEY)->andReturn($session);
        $session->shouldReceive('getSegment')->once()->andReturn($segment);

        $segment->shouldReceive('get')->once()->with('id_token')->andReturn(false);

        $request->shouldReceive('getHeaderLine')->once()->with(Cookies::COOKIE_HEADER)->andReturnNull();

        $url = $this->faker->url;

        $state = $this->faker->randomAscii;

        $this->authentication_service->shouldReceive('getAuthorizationUrl')->once()->andReturn($url);
        $this->authentication_service->shouldReceive('getState')->once()->andReturn($state);
        $segment->shouldReceive('set')->once()->with('state', $state);

        $response = $this->middleware->process($request, $delegate);

        $this->assertInstanceOf(RedirectResponse::class, $response);

        $this->assertSame([$url], $response->getHeader('Location'));
    }
}
