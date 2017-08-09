<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest\Authentication;

use Firebase\JWT\ExpiredException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Mockery as M;
use PHPUnit\Framework\TestCase;
use Stickee\Auth\Authentication\JWTProxy;
use Stickee\Auth\Authentication\Service;
use Stickee\Auth\Exception\InvalidIdTokenException;
use Stickee\AuthTest\FakeJWTTrait;
use Stickee\AuthTest\MockeryTrait;

class ServiceTest extends TestCase
{
    use FakeJWTTrait;
    use MockeryTrait;

    /**
     * @var \Faker\Generator $faker
     */
    private $faker;

    /**
     * @var JWTProxy|\Mockery\Mock $jwt
     */
    private $jwt;

    /**
     * @var AbstractProvider|\Mockery\Mock $provider_mock
     */
    private $provider_mock;

    /**
     * @var array $serviceconfig
     */
    private $serviceconfig;

    public function setUp()
    {
        $this->faker = \Faker\Factory::create();

        $this->provider_mock = M::mock(AbstractProvider::class);

        $this->serviceconfig = [
            'credentials' => [
                'key' => $this->faker->email,
                'secret' => $this->faker->password,
            ],
            'domain' => $this->faker->domainName,
        ];

        $this->jwt = M::mock(JWTProxy::class);
    }

    public function testGetIdToken()
    {
        /** @var AccessToken|\Mockery\Mock $accessToken */
        $accessToken = M::mock(AccessToken::class);

        $code = $this->faker->randomAscii;
        $this->provider_mock->shouldReceive('getAccessToken')->once()
            ->with('authorization_code', ['code' => $code])
            ->andReturn($accessToken);

        $fake_jwt_body = [
            'hd' => $this->serviceconfig['domain'],
            'aud' => $this->serviceconfig['credentials']['key'],
        ];

        $fake_jwt = $this->generateFakeJWT($fake_jwt_body);

        $accessToken->shouldReceive('getValues')->once()->andReturn(
            [
                'id_token' => $fake_jwt,
            ]
        );

        $fake_keys = [sha1($this->private_key) => $this->public_key];

        $this->jwt->shouldReceive('decode')->once()
            ->with($fake_jwt, $fake_keys, Service::ALLOWED_ALGS)
            ->andReturn($fake_jwt_body);

        $service = new Service($this->jwt, $this->provider_mock, $fake_keys);

        $service->getIdToken($code);
    }

    public function testInvalidToken()
    {
        $fake_keys = [sha1($this->private_key) => $this->public_key];

        $this->expectException(InvalidIdTokenException::class);

        $fake_jwt_body = [
            'hd' => $this->serviceconfig['domain'],
            'aud' => $this->serviceconfig['credentials']['key'],
            'exp' => time() - (24 * 60 * 60),
        ];

        $fake_jwt = $this->generateFakeJWT($fake_jwt_body);

        $this->jwt->shouldReceive('decode')->once()
            ->with($fake_jwt, $fake_keys, Service::ALLOWED_ALGS)
            ->andThrow(ExpiredException::class);

        $service = new Service($this->jwt, $this->provider_mock, $fake_keys);

        $service->validateIdToken($fake_jwt);
    }

    public function testInvalidTokenIsInvalid()
    {
        $fake_keys = [sha1($this->private_key) => $this->public_key];

        $fake_jwt_body = [
            'hd' => $this->serviceconfig['domain'],
            'aud' => $this->serviceconfig['credentials']['key'],
            'exp' => time() - (24 * 60 * 60),
        ];

        $fake_jwt = $this->generateFakeJWT($fake_jwt_body);

        $this->jwt->shouldReceive('decode')->once()
            ->with($fake_jwt, $fake_keys, Service::ALLOWED_ALGS)
            ->andThrow(ExpiredException::class);

        $service = new Service($this->jwt, $this->provider_mock, $fake_keys);

        $this->assertFalse($service->idTokenIsValid($fake_jwt));
    }

    public function testValidToken()
    {
        $fake_keys = [sha1($this->private_key) => $this->public_key];

        $fake_jwt_body = [
            'hd' => $this->serviceconfig['domain'],
            'aud' => $this->serviceconfig['credentials']['key'],
        ];

        $fake_jwt = $this->generateFakeJWT($fake_jwt_body);

        $this->jwt->shouldReceive('decode')->once()
            ->with($fake_jwt, $fake_keys, Service::ALLOWED_ALGS)
            ->andReturn($fake_jwt_body);

        $service = new Service($this->jwt, $this->provider_mock, $fake_keys);

        $service->validateIdToken($fake_jwt);
    }

    public function testValidTokenIsValid()
    {
        $fake_keys = [sha1($this->private_key) => $this->public_key];

        $fake_jwt_body = [
            'hd' => $this->serviceconfig['domain'],
            'aud' => $this->serviceconfig['credentials']['key'],
        ];

        $fake_jwt = $this->generateFakeJWT($fake_jwt_body);

        $this->jwt->shouldReceive('decode')->once()
            ->with($fake_jwt, $fake_keys, Service::ALLOWED_ALGS)
            ->andReturn($fake_jwt_body);

        $service = new Service($this->jwt, $this->provider_mock, $fake_keys);

        $this->assertTrue($service->idTokenIsValid($fake_jwt));
    }

    public function testValidTokenInvalidDomain()
    {
        $fake_keys = [sha1($this->private_key) => $this->public_key];

        $fake_jwt_body = [
            'hd' => $this->faker->domainName,
            'aud' => $this->serviceconfig['credentials']['key'],
        ];

        $fake_jwt = $this->generateFakeJWT($fake_jwt_body);

        $this->jwt->shouldReceive('decode')->once()
            ->with($fake_jwt, $fake_keys, Service::ALLOWED_ALGS)
            ->andReturn($fake_jwt_body);

        $this->expectException(InvalidIdTokenException::class);
        $this->expectExceptionMessage('Token is not a user of the domain for this application');

        $service = new Service($this->jwt, $this->provider_mock, $fake_keys, ['hd' => $this->faker->domainName]);

        $service->validateIdToken($fake_jwt);
    }

    public function testValidTokenInvalidAudience()
    {
        $fake_keys = [sha1($this->private_key) => $this->public_key];

        $fake_jwt_body = [
            'hd' => $this->serviceconfig['domain'],
            'aud' => $this->faker->email,
        ];

        $fake_jwt = $this->generateFakeJWT($fake_jwt_body);

        $this->jwt->shouldReceive('decode')->once()
            ->with($fake_jwt, $fake_keys, Service::ALLOWED_ALGS)
            ->andReturn($fake_jwt_body);

        $this->expectException(InvalidIdTokenException::class);
        $this->expectExceptionMessage('Token is not intended for this application');

        $service = new Service($this->jwt, $this->provider_mock, $fake_keys, ['aud' => $this->faker->email]);

        $service->validateIdToken($fake_jwt);
    }

    public function testGetAuthUrl()
    {
        $arr = [
            $this->faker->word,
            $this->faker->word,
            $this->faker->word,
            $this->faker->word,
            $this->faker->word,
            $this->faker->word,
        ];

        $url = $this->faker->url;

        $fake_keys = [sha1($this->private_key) => $this->public_key];

        $service = new Service($this->jwt, $this->provider_mock, $fake_keys);

        $this->provider_mock->shouldReceive('getAuthorizationUrl')->once()->with($arr)->andReturn($url);

        $this->assertSame($url, $service->getAuthorizationUrl($arr));
    }

    public function testGetState()
    {
        $str = $this->faker->randomAscii;

        $fake_keys = [sha1($this->private_key) => $this->public_key];

        $service = new Service($this->jwt, $this->provider_mock, $fake_keys);

        $this->provider_mock->shouldReceive('getState')->once()->andReturn($str);

        $this->assertSame($str, $service->getState());
    }
}
