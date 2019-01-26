<?php
/**
 * Copyright (c) 2017 Stickee Technology Limited
 * Copyright (c) 2017 - 2019 Martin Meredith <martin@sourceguru.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Mez\AuthTest\Authentication;

use Aura\Session\Segment;
use Aura\Session\Session;
use DaMess\Http\SessionMiddleware;
use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\SetCookies;
use Mez\Auth\Authentication\Middleware;
use Mez\Auth\Authentication\Service;
use Mez\AuthTest\FakeJWTTrait;
use Mez\AuthTest\MockeryTrait;
use Mockery as M;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;

class MiddlewareTest extends TestCase
{
    use FakeJWTTrait;
    use MockeryTrait;

    /**
     * @var Service|\Mockery\Mock|\Mockery\MockInterface $authentication_service
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
    public function setUp(): void
    {
        $this->faker = \Faker\Factory::create();
        $this->authentication_service = M::mock(Service::class);
        $this->middleware = new Middleware($this->authentication_service);
    }

    public function testProcess()
    {
        /** @var ServerRequestInterface|\Mockery\Mock $request */
        $request = M::mock(ServerRequestInterface::class);

        /** @var \Psr\Http\Server\RequestHandlerInterface|\Mockery\Mock $handler */
        $handler = M::mock(RequestHandlerInterface::class);

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

        $handler->shouldReceive('handle')->once()->with($request)->andReturn($response);

        $this->middleware->process($request, $handler);
    }

    public function testProcessNoToken()
    {
        /** @var ServerRequestInterface|\Mockery\Mock $request */
        $request = M::mock(ServerRequestInterface::class);

        /** @var \Psr\Http\Server\RequestHandlerInterface|\Mockery\Mock $handler */
        $handler = M::mock(RequestHandlerInterface::class);

        /** @var Session|\Mockery\Mock $session */
        $session = M::mock(Session::class);

        /** @var Segment|\Mockery\Mock $segment */
        $segment = M::mock(Segment::class);

        $request->shouldReceive('getAttribute')->once()->with(SessionMiddleware::KEY)->andReturn($session);
        $session->shouldReceive('getSegment')->once()->andReturn($segment);

        $segment->shouldReceive('get')->once()->with('id_token')->andReturn(false);

        $request->shouldReceive('getHeaderLine')->once()->with(Cookies::COOKIE_HEADER)->andReturn('');

        $url = $this->faker->url;

        $state = $this->faker->randomAscii;

        $this->authentication_service->shouldReceive('getAuthorizationUrl')->once()->andReturn($url);
        $this->authentication_service->shouldReceive('getState')->once()->andReturn($state);
        $segment->shouldReceive('set')->once()->with('state', $state);

        $response = $this->middleware->process($request, $handler);

        $this->assertInstanceOf(RedirectResponse::class, $response);

        $this->assertSame([$url], $response->getHeader('Location'));
    }
}
