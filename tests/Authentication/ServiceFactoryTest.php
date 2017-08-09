<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest\Authentication;

use League\OAuth2\Client\Provider\AbstractProvider;
use Mockery as M;
use PHPUnit\Framework\TestCase;
use Stickee\Auth\Authentication\JWTProxy;
use Stickee\Auth\Authentication\Service;
use Stickee\Auth\Authentication\ServiceFactory;
use Stickee\Auth\Oauth\Keys;
use Stickee\Auth\Oauth\Provider;
use Stickee\AuthTest\ContainerTrait;
use Stickee\AuthTest\MockeryTrait;

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
