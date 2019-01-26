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

namespace Mez\Auth\Oauth;

use DaMess\Http\SessionMiddleware;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Mez\Auth\Authentication\Service;
use Mez\Auth\Exception\InvalidIdTokenException;
use Mez\Auth\Exception\OauthLoginException;
use Mez\Auth\Exception\ReauthenticateException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class RedirectAction
 *
 * @package Mez\Auth\Oauth
 */
class RedirectAction implements MiddlewareInterface
{
    /**
     * @var string $redirect_to
     */
    private $redirect_to;

    /**
     * @var \Mez\Auth\Authentication\Service $service
     */
    private $service;

    /**
     * RedirectAction constructor.
     *
     * @param \Mez\Auth\Authentication\Service $service
     * @param string $redirect_to
     */
    public function __construct(Service $service, string $redirect_to)
    {
        $this->service = $service;
        $this->redirect_to = $redirect_to;
    }

    /**
     * process
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Aura\Session\Session $session */
        $session = $request->getAttribute(SessionMiddleware::KEY);

        $segment = $session->getSegment(Service::class);

        $query_parameters = $request->getQueryParams();

        $this->validateQueryParameters($query_parameters, $segment->get('state'));

        try {
            $id_token = $this->service->getIdToken($query_parameters['code']);
        } catch (Exception $e) {
            if ($e instanceof IdentityProviderException || $e instanceof InvalidIdTokenException) {
                throw new ReauthenticateException('Reauthentication Required', 0, $e);
            }

            throw $e;
        }

        $segment->set('id_token', $id_token);

        $session->commit();

        return new RedirectResponse($this->redirect_to);
    }

    /**
     * validateQueryParameters
     *
     * @param array $query_parameters
     * @param string $state
     *
     * @throws \Mez\Auth\Exception\OauthLoginException
     */
    private function validateQueryParameters(array $query_parameters, $state)
    {
        if (!empty($query_parameters['error'])) {
            throw new OauthLoginException(\sprintf('Oauth returned error %s', $query_parameters['error']));
        }

        if (empty($query_parameters['state'] || $query_parameters['state'] !== $state)) {
            throw new OauthLoginException('Invalid State was returned');
        }
    }
}
