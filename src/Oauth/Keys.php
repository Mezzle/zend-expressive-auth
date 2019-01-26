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

namespace Mez\Auth\Oauth;

use GuzzleHttp\ClientInterface;
use Interop\Container\ContainerInterface;
use Mez\Auth\Exception\InvalidKeyUrlException;

/**
 * Class Keys
 *
 * @package Mez\Auth\Oauth
 */
class Keys
{
    const HTTP_STATUS_FOUND = 200;

    /**
     * @var \GuzzleHttp\ClientInterface $client
     */
    private $client;

    /**
     * Keys constructor.
     *
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * __invoke
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return array|mixed
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var array $config */
        $config = $container->get('config')['stickee-auth'];

        $keys = [];

        if (isset($config['keys'])) {
            $keys = $config['keys'];
        } elseif (isset($config['keys_url'])) {
            $response = $this->client->request('GET', $config['keys_url']);

            if ($response->getStatusCode() !== self::HTTP_STATUS_FOUND) {
                throw new InvalidKeyUrlException(
                    \sprintf('Got error %d for %s', $response->getStatusCode(), $config['keys_url'])
                );
            }

            $keys = \json_decode($response->getBody(), true);
        }

        return $keys;
    }
}
