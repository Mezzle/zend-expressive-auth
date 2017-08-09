<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth;

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

    private function getDependencies()
    {
        return [
            'factories' => [
                Authentication\Middleware::class => Authentication\MiddlewareFactory::class,
                Authentication\Service::class => Authentication\ServiceFactory::class,
                Oauth\Keys::class => Oauth\Keys::class,
                Oauth\Provider::class => Oauth\Provider::class,
                Oauth\RedirectAction::class => Oauth\RedirectActionFactory::class,
            ],
            'invokables' => [
                Authentication\JWTProxy::class => Authentication\JWTProxy::class,
            ],
        ];
    }
}
