<?php

use Socialite\Bridge\Provider\FacebookProvider;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\Exception\OAuthException;
use Socialite\Component\OAuth\Exception\OAuthNetworkException;

require_once dirname(__DIR__).'/vendor/autoload.php';

// replace these values with your own
$callback_url  = 'http://my.app.com/callback.php';
$client_id     = 'facebook_client_id';
$client_secret = 'facebook_client_secret';

try {
    // create a provider instance
    $consumer = new OAuthConsumer($client_id, $client_secret);
    $provider = new FacebookProvider($consumer, $callback_url);
    // request an access token
    $provider->getAccessToken(array('code' => $_REQUEST['code'], 'redirect_uri' => $callback_url, 'grant_type' => 'authorization_code'));
    // get the response data
    $response = $provider->getResponse();
    // the rest is up to you
    var_dump($response);
} catch(OAuthException $e) {
    // generic exception
} catch(OAuthNetworkException $e) {
    // network exception
}
