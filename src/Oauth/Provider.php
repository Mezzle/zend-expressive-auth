<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Oauth;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class GoogleOauthFactory
 *
 * @package Stickee\Auth\Oauth
 */
class Provider
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     *
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')['stickee-auth']['oauth'];
        $credentials = $config['credentials'];

        /** @var ServerUrlHelper $server_url_helper */
        $server_url_helper = $container->get(ServerUrlHelper::class);

        /** @var UrlHelper $url_helper */
        $url_helper = $container->get(UrlHelper::class);

        $provider = $config['provider'];

        return new $provider(
            [
                'clientId' => $credentials['key'],
                'clientSecret' => $credentials['secret'],
                'redirectUri' => $server_url_helper->generate($url_helper->generate('oauth-redirect')),
                'hostedDomain' => $config['domain'],
            ]
        );
    }
}
