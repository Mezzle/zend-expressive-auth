<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Oauth;

use DaMess\Http\SessionMiddleware;
use Exception;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ServerRequestInterface;
use Stickee\Auth\Authentication\Service;
use Stickee\Auth\Exception\InvalidIdTokenException;
use Stickee\Auth\Exception\OauthLoginException;
use Stickee\Auth\Exception\ReauthenticateException;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class RedirectAction
 *
 * @package Stickee\Auth\Oauth
 */
class RedirectAction implements MiddlewareInterface
{
    /**
     * @var string $redirect_to
     */
    private $redirect_to;

    /**
     * @var \Stickee\Auth\Authentication\Service $service
     */
    private $service;

    /**
     * RedirectAction constructor.
     *
     * @param \Stickee\Auth\Authentication\Service $service
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
     * @param \Interop\Http\ServerMiddleware\DelegateInterface $delegate
     *
     * @return \Zend\Diactoros\Response\RedirectResponse
     *
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
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
     * @throws \Stickee\Auth\Exception\OauthLoginException
     */
    private function validateQueryParameters(array $query_parameters, $state)
    {
        if (!empty($query_parameters['error'])) {
            throw new OauthLoginException(sprintf('Oauth returned error %s', $query_parameters['error']));
        }

        if (empty($query_parameters['state'] || $query_parameters['state'] !== $state)) {
            throw new OauthLoginException('Invalid State was returned');
        }
    }
}
