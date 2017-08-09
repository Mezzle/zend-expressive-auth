<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Oauth;

use Interop\Container\ContainerInterface;
use Stickee\Auth\Authentication\Service;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class RedirectActionFactory
 *
 * @package Stickee\Auth\Oauth
 */
class RedirectActionFactory
{
    /**
     * __invoke
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return \Stickee\Auth\Oauth\RedirectAction
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var Service $service */
        $service = $container->get(Service::class);

        $config = $container->get('config')['stickee-auth'];

        /** @var \Zend\Expressive\Helper\UrlHelper $url_helper */
        $url_helper = $container->get(UrlHelper::class);

        $redirect_to = $url_helper->generate($config['redirect_to_route']);

        return new RedirectAction($service, $redirect_to);
    }
}
