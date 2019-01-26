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

namespace Mez\AuthTest;

use Interop\Container\ContainerInterface;
use Mockery as M;

trait ContainerTrait
{
    /**
     * getContainer
     *
     * @return ContainerInterface|\Mockery\Mock|\Mockery\MockInterface
     */
    private function getContainer()
    {
        return M::mock(ContainerInterface::class);
    }

    /**
     * containerGet
     *
     * @param ContainerInterface|\Mockery\Mock $container
     * @param string $requested_name
     * @param mixed $mock
     */
    private function containerGet($container, $requested_name, $mock)
    {
        $container->shouldReceive('get')->once()->with($requested_name)->andReturn($mock);
    }

    /**
     * containerGetMock
     *
     * @param ContainerInterface|\Mockery\Mock $container
     * @param string $class
     *
     * @return \Mockery\MockInterface|\Mockery\Mock
     */
    private function containerGetMock($container, $class)
    {
        $mock = M::mock($class);

        $this->containerGet($container, $class, $mock);

        return $mock;
    }

    /**
     * containerGetConfig
     *
     * @param $container
     * @param $config
     */
    private function containerGetConfig($container, $config)
    {
        $this->containerGet($container, 'config', $config);
    }
}
