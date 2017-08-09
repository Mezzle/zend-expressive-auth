<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest\Oauth;

use PHPUnit\Framework\TestCase;
use Stickee\Auth\Oauth\Provider;
use Stickee\AuthTest\ContainerTrait;
use Stickee\AuthTest\MockeryTrait;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

class ProviderTest extends TestCase
{
    use ContainerTrait;
    use MockeryTrait;

    public function testInvoke()
    {
        $faker = \Faker\Factory::create();

        $container = $this->getContainer();

        $config = [
            'stickee-auth' => [
                'oauth' => [
                    'credentials' => [
                        'key' => $faker->email,
                        'secret' => $faker->password,
                    ],
                    'domain' => $faker->domainName,
                    'provider' => MockProvider::class,
                ],
            ],
        ];

        $this->containerGetConfig(
            $container,
            $config
        );

        $server_url_helper = $this->containerGetMock($container, ServerUrlHelper::class);
        $url_helper = $this->containerGetMock($container, UrlHelper::class);

        $slug = $faker->slug;
        $url_helper->shouldReceive('generate')->with('oauth-redirect')->andReturn($slug);
        $server_url_helper->shouldReceive('generate')->with($slug)->andReturn($faker->url);

        $factory = new Provider();

        $this->assertInstanceOf(MockProvider::class, $factory($container));
    }
}
