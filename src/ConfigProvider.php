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
                \Stickee\Auth\Oauth\Keys::class => \Stickee\Auth\Oauth\Keys::class,
            ],
        ];
    }
}
