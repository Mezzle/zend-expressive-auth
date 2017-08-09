<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Authentication;

use DaMess\Http\SessionMiddleware;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class Middleware
 *
 * @package Stickee\Auth\Authentication
 */
class Middleware implements MiddlewareInterface
{
    /**
     * @var \Stickee\Auth\Authentication\Service $authentication_service
     */
    private $authentication_service;

    /**
     * Middleware constructor.
     *
     * @param \Stickee\Auth\Authentication\Service $authentication_service
     */
    public function __construct(Service $authentication_service)
    {
        $this->authentication_service = $authentication_service;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \ErrorException
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /** @var \Aura\Session\Session $session */
        $session = $request->getAttribute(SessionMiddleware::KEY);

        $segment = $session->getSegment(Service::class);

        $id_token = $segment->get('id_token');

        if (!$id_token) {
            $id_token = FigRequestCookies::get($request, 'jwt', false)->getValue();
        }

        if (!$id_token || !$this->authentication_service->idTokenIsValid($id_token)) {
            $url = $this->authentication_service->getAuthorizationUrl();

            $segment->set('state', $this->authentication_service->getState());

            return new RedirectResponse($url);
        }

        $response = $delegate->process($request);

        return FigResponseCookies::set(
            $response,
            SetCookie::create('jwt')->withValue($id_token)->rememberForever()
        );
    }
}
