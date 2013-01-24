Socialite - OAuth library for PHP 5.3+
======================================

Socialite is written with speed and flexibility in mind. It allows developers to build
better and easy to maintain OAuth applications with PHP.

Usage
-----

**login.php**
```php
<?php

use Socialite\Bridge\Provider\FacebookProvider;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\Exception\OAuthException;
use Socialite\Component\OAuth\Exception\OAuthNetworkException;

try {
    // create a provider instance
    $consumer = new OAuthConsumer('client_id', 'client_secret');
    $provider = new FacebookProvider($consumer);
    // set the oauth_callback value
    $provider->setCallback('http://my.app.com/callback');
    // define the scope of the request
    $provider->setScope(array('email'));
    // redirect the user to the dialog URL
    header('Location: ' . $provider->getAuthorizeUrl());
} catch(OAuthException $e) {
    // generic exception
} catch(OAuthNetworkException $e) {
    // network exception
}
```

**callback.php**
```php
<?php

use Socialite\Bridge\Provider\FacebookProvider;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\Exception\OAuthException;
use Socialite\Component\OAuth\Exception\OAuthNetworkException;

try {
    // create a provider instance
    $consumer = new OAuthConsumer('client_id', 'client_secret');
    $token    = new OAuthToken($_REQUEST['oauth_token']);
    $provider = new FacebookProvider($consumer);
    // set the oauth_callback value
    $provider->setCallback('http://my.app.com/callback');
    // request an access token
    $provider->getAccessToken(array('code' => $_REQUEST['code'], 'redirect_uri' => $callback_url, 'grant_type' => 'authorization_code'));
    // get the response data
    $response $provider->getResponse();
    // the rest is up to you...
} catch(OAuthException $e) {
    // generic exception
} catch(OAuthNetworkException $e) {
    // network exception
}
```

Requirements
------------

- Any flavor of PHP 5.3 and above should do

Submitting bugs and feature requests
------------------------------------

Bugs and feature request are tracked on [GitHub](https://github.com/telemundo/socialite/issues)

Author
------

Rodolfo Puig - [@rudisimo](https://twitter.com/rudisimo)  

License
-------

This package is licensed under the MIT License - see the LICENSE file for details.

Acknowledgements
----------------

This library is heavily inspired by the [Phirehose](https://github.com/fennb/phirehose),
[twitteroauth](https://github.com/abraham/twitteroauth) and [oauth-php](http://code.google.com/p/oauth-php)
libraries and many other libraries out there; although most concepts have been adjusted to fit to the PHP 5.3+ namespace
paradigm.
