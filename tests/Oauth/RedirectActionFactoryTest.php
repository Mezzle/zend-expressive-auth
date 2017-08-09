<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest\Oauth;

use PHPUnit\Framework\TestCase;
use Stickee\Auth\Authentication\Service;
use Stickee\Auth\Oauth\RedirectAction;
use Stickee\Auth\Oauth\RedirectActionFactory;
use Stickee\AuthTest\ContainerTrait;
use Stickee\AuthTest\MockeryTrait;
use Zend\Expressive\Helper\UrlHelper;

class RedirectActionFactoryTest extends TestCase
{
    use ContainerTrait;
    use MockeryTrait;

    public function testInvoke()
    {
        $container = $this->getContainer();

        $this->containerGetMock($container, Service::class);
        $url_helper = $this->containerGetMock($container, UrlHelper::class);
        $this->containerGetConfig($container, ['stickee-auth' => ['redirect_to_route' => 'home']]);

        $url_helper->shouldReceive('generate')->with('home')->andReturn('http://www.stickee.co.uk/');

        $factory = new RedirectActionFactory();

        $this->assertInstanceOf(RedirectAction::class, $factory($container));
    }
}
