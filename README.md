# Socialite #

Social Networking for PHP 5.3+

## Usage ##

```php
use Socialite\Bridge\Twitter\Request;
use Socialite\Bridge\Twitter\Api\RestClient;
use Socialite\Component\OAuth\Exception\OAuthException;
use Socialite\Component\OAuth\Exception\OAuthNetworkException;

try {
    // create the rest client and perform a user lookup request
    $rc   = new RestClient(CONSUMER_KEY, CONSUMER_SECRET, USER_TOKEN, USER_SECRET);
    $url  = $rc->createUrl(RestClient::GET_USERS_LOOKUP, Request::TYPE_JSON);
    $json = $rc->get($url, array('screen_username' => 'rpuig_nbcuni'));
} catch(OAuthException $e) {
    // generic exception
} catch(OAuthNetworkException $e) {
    // network exception
}
```

## Requirements ##

- Any flavor of PHP 5.3 or greater should do
- A PSR-0 class autoloader ([Symfony ClassLoader](https://github.com/symfony/ClassLoader))

## Submitting bugs and feature requests ##

Bugs and feature request are tracked on [GitHub](https://github.com/telemundo/socialite/issues)

## Author ##

Rodolfo Puig - <rodolfo.puig@nbcuni.com> - <https://twitter.com/rpuig_nbcuni><br />

## License ##

Socialite is licensed under the MIT License - see the LICENSE file for details

## Acknowledgements ##

This library is heavily inspired by the [Phirehose](https://github.com/fennb/phirehose), [twitteroauth](https://github.com/abraham/twitteroauth) and [oauth-php](http://code.google.com/p/oauth-php) libraries, although most concepts have been adjusted to fit to the PHP 5.3+ namespace paradigm.
