<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Oauth;

use Interop\Container\ContainerInterface;

/**
 * Class Keys
 *
 * @package Stickee\Auth\Oauth
 */
class Keys
{
    /**
     * __invoke
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return array
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var array $config */
        $config = $container->get('config')['stickee-auth'];

        $keys = [];

        if (isset($config['keys'])) {
            $keys = $config['keys'];
        } elseif (isset($config['keys_url'])) {
            $keys = json_decode(file_get_contents($config['keys_url']), true);
        }

        return $keys;
    }
}
