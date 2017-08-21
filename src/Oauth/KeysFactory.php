<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Oauth;

/**
 * Class KeysFactory
 *
 * @package Stickee\Auth\Oauth
 */
class KeysFactory
{
    /**
     * __invoke
     *
     * @return \Stickee\Auth\Oauth\Keys
     */
    public function __invoke()
    {
        return new Keys(new \GuzzleHttp\Client());
    }
}
