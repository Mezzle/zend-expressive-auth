# Zend Expressive Authentication

[![Build Status](https://travis-ci.org/stickeeuk/zend-expressive-auth.svg?branch=master)](https://travis-ci.org/stickeeuk/zend-expressive-auth)

Authentication for Logging in with to an expressive application


This will need you to add `\Mez\Auth\ConfigProvider` to your main config.


At the moment, this is designed to protect individual routes.

To do so, you simply need to add the Middleware to the chain for that route

```php
<?php

use Mez\Auth\Authentication\Middleware as AuthenticationMiddleware;

$app->post(
    '/',
    [
        AuthenticationMiddleware::class,
        ProtectedAction::class,
    ],
    'protected'
);
```

But make sure you don't add it to the route that redirects you out!

```php
<?php

use \Mez\Auth\Oauth\RedirectAction;

$app->get('/login/redirect', RedirectAction::class, 'oauth-redirect');
```
