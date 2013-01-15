Socialite - Social networking for PHP 5.3+
==========================================

Socialite is written with speed and flexibility in mind. It allows developers to build
better and easy to maintain OAuth requests with PHP.

This library implements the [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
interface that describes the mandatory requirements that must be adhered to for autoloader
interoperability.

Usage
-----

```php
<?php
use Socialite\Bridge\Twitter\Request;
use Socialite\Bridge\Twitter\Api\RestClient;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthToken;
use Socialite\Component\OAuth\Exception\OAuthException;
use Socialite\Component\OAuth\Exception\OAuthNetworkException;

try {
    // create the rest client and perform a user lookup request
    $restClient = new RestClient(
        new OAuthConsumer(CONSUMER_KEY, CONSUMER_SECRET),
        new OAuthToken(ACCESS_TOKEN, ACCESS_TOKEN_SECRET)
    );
    $requestUrl = $restClient->createUrl(RestClient::GET_USERS_LOOKUP, Request::TYPE_JSON);
    $response   = $restClient->get($requestUrl, array('screen_username' => 'rpuig_nbcuni'));
} catch(OAuthException $e) {
    // generic exception
} catch(OAuthNetworkException $e) {
    // network exception
}
```

Requirements
------------

- Any flavor of PHP 5.3 or above should do

Submitting bugs and feature requests
------------------------------------

Bugs and feature request are tracked on [GitHub](https://github.com/telemundo/socialite/issues)

Author
------

Rodolfo Puig - [@rpuig_nbcuni](https://twitter.com/rpuig_nbcuni)  

License
-------

This package is licensed under the MIT License - see the LICENSE file for details.

Acknowledgements
----------------

This library is heavily inspired by the [Phirehose](https://github.com/fennb/phirehose),
[twitteroauth](https://github.com/abraham/twitteroauth) and [oauth-php](http://code.google.com/p/oauth-php)
libraries, although most concepts have been adjusted to fit to the PHP 5.3+ namespace
paradigm.