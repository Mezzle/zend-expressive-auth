<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Authentication;

use Interop\Container\ContainerInterface;

class ServiceFactory
{
    /**
     * __invoke
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return \Stickee\Auth\Authentication\Service
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var \League\OAuth2\Client\Provider\AbstractProvider $provider */
        $provider = $container->get(\Stickee\Auth\Oauth\Provider::class);

        /** @var array $keys */
        $keys = $container->get(\Stickee\Auth\Oauth\Keys::class);

        /** @var \Stickee\Auth\Authentication\JWTProxy $jwt_proxy */
        $jwt_proxy = $container->get(JWTProxy::class);

        $options = $container->get('config')['stickee-auth']['options'];

        return new Service($jwt_proxy, $provider, $keys, $options);
    }
}
