<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth;

/**
 * Class ConfigProvider
 *
 * @package Stickee\Auth
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'stickee-auth' => include __DIR__ . '/../config/config.php',
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * getDependencies
     *
     * @return array
     */
    private function getDependencies()
    {
        return [
            'factories' => [
                Authentication\Middleware::class => Authentication\MiddlewareFactory::class,
                Authentication\Service::class => Authentication\ServiceFactory::class,
                Oauth\Keys::class => Oauth\KeysFactory::class,
                Oauth\RedirectAction::class => Oauth\RedirectActionFactory::class,
            ],
            'invokables' => [
                Authentication\JWTProxy::class => Authentication\JWTProxy::class,
                Oauth\Provider::class => Oauth\Provider::class,
            ],
        ];
    }
}
