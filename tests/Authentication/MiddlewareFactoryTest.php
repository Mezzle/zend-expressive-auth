<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest\Authentication;

use PHPUnit\Framework\TestCase;
use Stickee\Auth\Authentication\Middleware;
use Stickee\Auth\Authentication\MiddlewareFactory;
use Stickee\Auth\Authentication\Service;
use Stickee\AuthTest\ContainerTrait;
use Stickee\AuthTest\MockeryTrait;

class MiddlewareFactoryTest extends TestCase
{
    use ContainerTrait;
    use MockeryTrait;

    public function testInvoke()
    {
        $container = $this->getContainer();
        $this->containerGetMock($container, Service::class);

        $factory = new MiddlewareFactory();

        $this->assertInstanceOf(Middleware::class, $factory($container));
    }
}
