<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest\Oauth;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Mockery as M;
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

        $client = new Client();
        $factory = new Keys($client);

        $this->assertSame($config['stickee-auth']['keys'], $factory($container));
    }

    public function testInvokeKeysUrl()
    {
        $client = new Client();
        $response = $client->get('https://www.googleapis.com/oauth2/v1/certs');

        $container = $this->getContainer();
        $config = ['stickee-auth' => ['keys_url' => 'https://www.googleapis.com/oauth2/v1/certs']];
        $this->containerGetConfig($container, $config);

        /** @var \Mockery\Mock $guzzle */
        $guzzle = M::mock(ClientInterface::class);
        $guzzle->shouldReceive('request')
            ->with('GET', $config['stickee-auth']['keys_url'])
            ->andReturn($response);

        $factory = new Keys($guzzle);

        $decoded = json_decode($response->getBody(), true);

        $this->assertSame($decoded, $factory($container));
    }
}
