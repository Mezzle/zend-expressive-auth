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
use Mez\Auth\Authentication\Service;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class RedirectActionFactory
 *
 * @package Mez\Auth\Oauth
 */
class RedirectActionFactory
{
    /**
     * __invoke
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return \Mez\Auth\Oauth\RedirectAction
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
