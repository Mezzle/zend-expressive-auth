<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\Auth\Oauth;

use GuzzleHttp\ClientInterface;
use Interop\Container\ContainerInterface;
use Stickee\Auth\Exception\InvalidKeyUrlException;

/**
 * Class Keys
 *
 * @package Stickee\Auth\Oauth
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
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Stickee\Auth\Exception\InvalidKeyUrlException
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
                    sprintf('Got error %d for %s', $response->getStatusCode(), $config['keys_url'])
                );
            }

            $keys = json_decode($response->getBody(), true);
        }

        return $keys;
    }
}
