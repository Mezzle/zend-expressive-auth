<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Authentication;

use Interop\Container\ContainerInterface;

/**
 * Class MiddlewareFactory
 *
 * @package Stickee\Auth\Authentication
 */
class MiddlewareFactory
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     *
     * @return \Stickee\Auth\Authentication\Middleware
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        $authentication_service = $container->get(Service::class);

        return new Middleware($authentication_service);
    }
}
