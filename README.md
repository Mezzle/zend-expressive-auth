# Zend Expressive Authentication
[![All Contributors](https://img.shields.io/badge/all_contributors-1-orange.svg?style=flat-square)](#contributors)

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

## Contributors

Thanks goes to these wonderful people ([emoji key](https://github.com/all-contributors/all-contributors#emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore -->
| [<img src="https://avatars3.githubusercontent.com/u/570639?v=4" width="100px;" alt="Martin Meredith"/><br /><sub><b>Martin Meredith</b></sub>](https://www.sourceguru.net)<br />[💻](https://github.com/Mezzle/zend-expressive-auth/commits?author=mezzle "Code") [🚧](#maintenance-mezzle "Maintenance") [⚠️](https://github.com/Mezzle/zend-expressive-auth/commits?author=mezzle "Tests") |
| :---: |
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!