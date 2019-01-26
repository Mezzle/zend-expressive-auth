<?php
/**
 * Copyright (c) 2017 Stickee Technology Limited
 * Copyright (c) 2017 - 2019 Martin Meredith <martin@sourceguru.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Mez\Auth\Oauth;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class GoogleOauthFactory
 *
 * @package Mez\Auth\Oauth
 */
class Provider
{
    /**
     * Create an object
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return mixed
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
