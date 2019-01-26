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

use DomainException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use UnexpectedValueException;

/**
 * Class JWTProxy
 *
 * This class is provided so that we can inject a Mock JWT object into
 * code for testing.
 *
 * @package Mez\Auth\Authentication
 * @codeCoverageIgnore Ignore Code coverage as this is a proxy class
 */
class JWTProxy
{
    /**
     * Decodes a JWT string into a PHP object.
     *
     * @param string $jwt The JWT
     * @param string|array $key The key, or map of keys.
     *                          If the algorithm used is asymmetric, this is the public key
     * @param array $allowed_algs List of supported verification algorithms
     *                            Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
     *
     * @throws UnexpectedValueException Provided JWT was invalid
     * @throws SignatureInvalidException Provided JWT was invalid because the signature verification failed
     * @throws BeforeValidException Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException Provided JWT is trying to be used before it's been created as defined by
     *                              'iat'
     * @throws ExpiredException Provided JWT has since expired, as defined by the 'exp' claim
     *
     * @return object The JWT's payload as a PHP object
     *
     * @uses jsonDecode
     * @uses urlsafeB64Decode
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function decode($jwt, $key, $allowed_algs = [])
    {
        return JWT::decode($jwt, $key, $allowed_algs);
    }

    /**
     * Converts and signs a PHP object or array into a JWT string.
     *
     * @param object|array $payload PHP object or array
     * @param string $key The secret key.
     *                    If the algorithm used is asymmetric, this is the private key
     * @param string $alg The signing algorithm.
     *                    Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
     * @param mixed $keyId
     * @param array $head An array with header elements to attach
     *
     * @return string A signed JWT
     *
     * @uses jsonEncode
     * @uses urlsafeB64Encode
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function encode($payload, $key, $alg = 'HS256', $keyId = null, $head = null)
    {
        return JWT::encode($payload, $key, $alg, $keyId, $head);
    }

    /**
     * Sign a string with a given key and algorithm.
     *
     * @param string $msg The message to sign
     * @param string|resource $key The secret key
     * @param string $alg The signing algorithm.
     *                    Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
     *
     * @throws DomainException Unsupported algorithm was specified
     *
     * @return string An encrypted message
     *
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function sign($msg, $key, $alg = 'HS256')
    {
        return JWT::sign($msg, $key, $alg);
    }

    /**
     * Decode a JSON string into a PHP object.
     *
     * @param string $input JSON string
     *
     * @throws DomainException Provided string was invalid JSON
     *
     * @return object Object representation of JSON string
     *
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function jsonDecode($input)
    {
        return JWT::jsonDecode($input);
    }

    /**
     * Encode a PHP object into a JSON string.
     *
     * @param object|array $input A PHP object or array
     *
     * @throws DomainException Provided object could not be encoded to valid JSON
     *
     * @return string JSON representation of the PHP object or array
     *
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function jsonEncode($input)
    {
        return JWT::jsonEncode($input);
    }

    /**
     * Decode a string with URL-safe Base64.
     *
     * @param string $input A Base64 encoded string
     *
     * @return string A decoded string
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function urlsafeB64Decode($input)
    {
        return JWT::urlsafeB64Decode($input);
    }

    /**
     * Encode a string with URL-safe Base64.
     *
     * @param string $input The string you want encoded
     *
     * @return string The base64 encode of what you passed in
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function urlsafeB64Encode($input)
    {
        return JWT::urlsafeB64Encode($input);
    }
}
