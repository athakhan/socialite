Socialite
=========

Socialite is written with speed and flexibility in mind. It allows developers to build
better and easy to maintain OAuth applications with PHP.


Installation
------------

Since Socialite uses [Composer][1] to manage its dependencies, the recommended way
to install the libray is by following the steps below. Please note that the installation of
[Composer][1] is beyond the scope of this manual.

Begin by adding the following entry to your `composer.json` file:

    "require": {
        "telemundo/socialite": "dev-master"
    }

Run `php composer.phar update` to install the necessary dependencies.


Usage
-----

Use the following example to help you learn how to use the library.


##### connect.php

    <?php

    use Socialite\Bridge\Provider\FacebookProvider;
    use Socialite\Component\OAuth\OAuthConsumer;
    use Socialite\Component\OAuth\Exception\OAuthException;
    use Socialite\Component\OAuth\Exception\OAuthNetworkException;

    try {
        // create a provider instance
        $callback = 'http://my.app.com/callback.php';
        $consumer = new OAuthConsumer('client_id', 'client_secret');
        $provider = new FacebookProvider($consumer, $callback);
        // define the scope of the request
        $provider->setScope(array('email'));
        // redirect the user to the dialog URL
        header('Location: ' . $provider->getAuthorizeUrl());
    } catch(OAuthException $e) {
        // generic exception
    } catch(OAuthNetworkException $e) {
        // network exception
    }

##### callback.php

    <?php

    use Socialite\Bridge\Provider\FacebookProvider;
    use Socialite\Component\OAuth\OAuthConsumer;
    use Socialite\Component\OAuth\Exception\OAuthException;
    use Socialite\Component\OAuth\Exception\OAuthNetworkException;

    try {
        // create a provider instance
        $callback = 'http://my.app.com/callback.php';
        $consumer = new OAuthConsumer('client_id', 'client_secret');
        $provider = new FacebookProvider($consumer, $callback);
        // create a request token
        $token = new OAuthToken($_REQUEST['oauth_token']);
        $provider->setToken($token);
        // request an access token
        $provider->getAccessToken(array('code' => $_REQUEST['code'], 'redirect_uri' => $callback, 'grant_type' => 'authorization_code'));
        // get the response data; the rest is up to you...
        $response = $provider->getResponse();
    } catch(OAuthException $e) {
        // generic exception
    } catch(OAuthNetworkException $e) {
        // network exception
    }

Authors
-------

[Rodolfo Puig][1]  


License
-------

This package is licensed under the MIT License - see the LICENSE.txt file for details.


Acknowledgements
----------------

This library is heavily inspired by the [Phirehose][2], [twitteroauth][3] and [oauth-php][4]
libraries and many other libraries out there.


[1]: https://twitter.com/rudisimo "Follow @rudisimo on Twitter"
[2]: https://github.com/fennb/phirehose "Phirehose"
[3]: https://github.com/abraham/twitteroauth "twitteroauth"
[4]: http://code.google.com/p/oauth-php "oauth-php"