<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest;

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
