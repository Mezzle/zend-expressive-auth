<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest\Oauth;

use PHPUnit\Framework\TestCase;
use Stickee\Auth\Oauth\Keys;
use Stickee\AuthTest\ContainerTrait;
use Stickee\AuthTest\MockeryTrait;

class KeysTest extends TestCase
{
    use ContainerTrait;
    use MockeryTrait;

    public function testInvokeKeys()
    {
        $faker = \Faker\Factory::create();

        $container = $this->getContainer();
        $config = [
            'stickee-auth' => [
                'keys' => [
                    $faker->md5 => $faker->randomAscii,
                    $faker->md5 => $faker->randomAscii,
                    $faker->md5 => $faker->randomAscii,
                ],
            ],
        ];
        $this->containerGetConfig($container, $config);

        $factory = new Keys();

        $this->assertSame($config['stickee-auth']['keys'], $factory($container));
    }

    public function testInvokeKeysUrl()
    {
        $faker = \Faker\Factory::create();

        $container = $this->getContainer();
        $config = ['stickee-auth' => ['keys_url' => 'https://www.googleapis.com/oauth2/v1/certs']];
        $this->containerGetConfig($container, $config);

        $decoded = json_decode(file_get_contents($config['stickee-auth']['keys_url']), true);
        $factory = new Keys();

        $this->assertSame($decoded, $factory($container));
    }
}
