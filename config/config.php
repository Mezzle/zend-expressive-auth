<?php

$key = getenv('GOOGLE_CLIENT_KEY');
$secret = getenv('GOOGLE_CLIENT_SECRET');
$domain = getenv('APP_LOGIN_DOMAIN');

$config = [
    'keys_url' => 'https://www.googleapis.com/oauth2/v1/certs',
    'oauth' => [
        'credentials' => [
            'key' => $key,
            'secret' => $secret,
        ],
        'provider' => \League\OAuth2\Client\Provider\GenericProvider::class,
    ],
    'options' => [
        'aud' => $key,
    ],
    'redirect_to_route' => 'home',

];

if ($domain) {
    $config['oauth']['domain'] = $domain;
    $config['options']['hd'] = $domain;
}

return $config;
