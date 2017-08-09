<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Authentication;

use League\OAuth2\Client\Provider\AbstractProvider;
use Stickee\Auth\Exception\InvalidIdTokenException;
use UnexpectedValueException;

class Service
{
    const ALLOWED_ALGS = ['RS256'];

    /**
     * @var \Stickee\Auth\Authentication\JWTProxy $jwt
     */
    private $jwt;

    /**
     * @var array $keys
     */
    private $keys;

    /**
     * @var array $options
     */
    private $options;

    /**
     * @var \League\OAuth2\Client\Provider\AbstractProvider $provider
     */
    private $provider;

    /**
     * Service constructor.
     *
     * @param \Stickee\Auth\Authentication\JWTProxy $jwt
     * @param \League\OAuth2\Client\Provider\AbstractProvider $provider
     * @param array $keys
     * @param array $options
     */
    public function __construct(
        JWTProxy $jwt,
        AbstractProvider $provider,
        array $keys,
        array $options = []
    ) {
        $this->jwt = $jwt;
        $this->options = $options;
        $this->keys = $keys;
        $this->provider = $provider;
    }

    /**
     * idTokenIsValid
     *
     * @param string $token
     *
     * @return bool
     *
     * @throws \ErrorException
     */
    public function idTokenIsValid(string $token): bool
    {
        try {
            $this->validateIdToken($token);
        } catch (InvalidIdTokenException $e) {
            return false;
        }

        return true;
    }

    /**
     * validateIdToken
     *
     * @param string $id_token
     *
     * @throws \Stickee\Auth\Exception\InvalidIdTokenException
     */
    public function validateIdToken(string $id_token): void
    {
        try {
            $decoded = (array)$this->jwt->decode($id_token, $this->keys, self::ALLOWED_ALGS);
        } catch (UnexpectedValueException $e) {
            throw new InvalidIdTokenException('Unable to validate Id Token', 0, $e);
        }

        if (isset($this->options['aud']) && $this->options['aud'] != $decoded['aud']) {
            throw new InvalidIdTokenException('Token is not intended for this application');
        }

        if (isset($this->options['hd']) && $this->options['hd'] != $decoded['hd']) {
            throw new InvalidIdTokenException('Token is not a user of the domain for this application');
        }
    }

    /**
     * getAuthorizationUrl
     *
     * @param array $options
     *
     * @return string
     */
    public function getAuthorizationUrl(array $options = []): string
    {
        return $this->provider->getAuthorizationUrl($options);
    }

    /**
     * getState
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->provider->getState();
    }

    /**
     * getIdToken
     *
     * @param string $code
     *
     * @return string
     *
     * @throws \ErrorException
     * @throws \Stickee\Auth\Exception\InvalidIdTokenException
     */
    public function getIdToken(string $code): string
    {
        $access_token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
        $id_token = $access_token->getValues()['id_token'];

        $this->validateIdToken($id_token);

        return $id_token;
    }
}
