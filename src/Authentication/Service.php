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

namespace Mez\Auth\Authentication;

use League\OAuth2\Client\Provider\AbstractProvider;
use Mez\Auth\Exception\InvalidIdTokenException;
use UnexpectedValueException;

/**
 * Class Service
 *
 * @package Mez\Auth\Authentication
 */
class Service
{
    const ALLOWED_ALGS = ['RS256'];

    /**
     * @var \Mez\Auth\Authentication\JWTProxy $jwt
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
     * @param \Mez\Auth\Authentication\JWTProxy $jwt
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
     * @throws \ErrorException
     *
     * @return bool
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
     * @throws \Mez\Auth\Exception\InvalidIdTokenException
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
     * @throws \ErrorException
     * @throws \Mez\Auth\Exception\InvalidIdTokenException
     *
     * @return string
     */
    public function getIdToken(string $code): string
    {
        $access_token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
        $id_token = $access_token->getValues()['id_token'];

        $this->validateIdToken($id_token);

        return $id_token;
    }
}
