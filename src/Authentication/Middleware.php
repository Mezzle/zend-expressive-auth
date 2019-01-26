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

namespace Mez\Auth\Authentication;

use DaMess\Http\SessionMiddleware;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class Middleware
 *
 * @package Mez\Auth\Authentication
 */
class Middleware implements MiddlewareInterface
{
    /**
     * @var \Mez\Auth\Authentication\Service $authentication_service
     */
    private $authentication_service;

    /**
     * Middleware constructor.
     *
     * @param \Mez\Auth\Authentication\Service $authentication_service
     */
    public function __construct(Service $authentication_service)
    {
        $this->authentication_service = $authentication_service;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @throws \ErrorException
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Aura\Session\Session $session */
        $session = $request->getAttribute(SessionMiddleware::KEY);

        $segment = $session->getSegment(Service::class);

        $id_token = $segment->get('id_token');

        if (!$id_token) {
            $id_token = FigRequestCookies::get($request, 'jwt', null)->getValue();
        }

        if (!$id_token || !$this->authentication_service->idTokenIsValid($id_token)) {
            $url = $this->authentication_service->getAuthorizationUrl();

            $segment->set('state', $this->authentication_service->getState());

            return new RedirectResponse($url);
        }

        $response = $handler->handle($request);

        return FigResponseCookies::set(
            $response,
            SetCookie::create('jwt')->withValue($id_token)->rememberForever()
        );
    }
}
