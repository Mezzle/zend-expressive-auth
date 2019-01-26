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

namespace Mez\AuthTest\Authentication;

use League\OAuth2\Client\Provider\AbstractProvider;
use Mez\Auth\Authentication\JWTProxy;
use Mez\Auth\Authentication\Service;
use Mez\Auth\Authentication\ServiceFactory;
use Mez\Auth\Oauth\Keys;
use Mez\Auth\Oauth\Provider;
use Mez\AuthTest\ContainerTrait;
use Mez\AuthTest\MockeryTrait;
use Mockery as M;
use PHPUnit\Framework\TestCase;

class ServiceFactoryTest extends TestCase
{
    use ContainerTrait;
    use MockeryTrait;

    public function testInvoke()
    {
        $container = $this->getContainer();

        $this->containerGet($container, Provider::class, M::mock(AbstractProvider::class));
        $this->containerGet($container, Keys::class, []);
        $this->containerGet($container, 'config', ['stickee-auth' => ['oauth' => [], 'options' => []]]);
        $this->containerGetMock($container, JWTProxy::class);

        $factory = new ServiceFactory();
        $this->assertInstanceOf(Service::class, $factory($container));
    }
}
