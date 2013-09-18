<?php

use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthToken;
use Socialite\Component\OAuth\Exception\OAuthException;
use Socialite\Component\OAuth\Exception\OAuthNetworkException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once dirname(__DIR__).'/vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views'
));

$app->get('/', function(Request $request) use ($app) {
    return $app['twig']->render('index.twig', array(
        'providers' => array(
            'facebook'   => array('Facebook', 'Client ID', 'Client Secret'),
            'google'     => array('Google', 'Client ID', 'Client Secret'),
            'instagram'  => array('Instagram', 'Client ID', 'Client Secret'),
            'linkedin'   => array('LinkedIn', 'Client ID', 'Client Secret'),
            'microsoft'  => array('Windows Live', 'Client ID', 'Client Secret'),
            'soundcloud' => array('SoundCloud', 'Client ID', 'Client Secret'),
            'twitter'    => array('Twitter', 'Consumer Key', 'Consumer Secret'),
            'yahoo'      => array('Yahoo', 'Client ID', 'Client Secret')
        )
    ));
})->bind('index');

$app->match('/oauth/{action}/{provider}', function(Request $request, $action, $provider) use ($app) {
    // create provider defaults
    switch($provider) {
        case 'facebook':
            $provider_class = '\Socialite\Bridge\Provider\FacebookProvider';
            $client_scope   = array('email');
            break;
        case 'google':
            $provider_class = '\Socialite\Bridge\Provider\GoogleProvider';
            $client_scope   = array('https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email');
            break;
        case 'instagram':
            $provider_class = '\Socialite\Bridge\Provider\InstagramProvider';
            $client_scope   = array('basic');
            break;
        case 'linkedin':
            //$provider_class = '\Socialite\Bridge\Provider\LinkedInProvider';
            break;
        case 'microsoft':
            $provider_class = '\Socialite\Bridge\Provider\MicrosoftProvider';
            $client_scope   = array('wl.signin', 'wl.basic', 'wl.emails');
            break;
        case 'soundcloud':
            $provider_class = '\Socialite\Bridge\Provider\SoundcloudProvider';
            $client_scope   = array('non-expiring');
            break;
        case 'twitter':
            $provider_class = '\Socialite\Bridge\Provider\TwitterProvider';
            break;
        case 'yahoo':
            //$provider_class = '\Socialite\Bridge\Provider\YahooProvider';
            break;
    }
    if (isset($provider_class)) {
        // create dynamic request parameters
        $client_id       = $request->get('client_id', $app['session']->get('client_id'));
        $client_secret   = $request->get('client_secret', $app['session']->get('client_secret'));
        $callback_params = array('action' => 'callback', 'provider' => $provider);
        $callback_url    = $app['url_generator']->generate('oauth', $callback_params, true);
        // create dynamic provider instance
        $consumer = new OAuthConsumer($client_id, $client_secret);
        $provider = new $provider_class($consumer, $callback_url);
        // perform oauth action
        if ($action == 'connect') {
            $app['session']->set('client_id', $client_id);
            $app['session']->set('client_secret', $client_secret);
            // setup the initial request
            if ($provider->getVersion() == '2.0') {
                if (isset($client_scope)) {
                    $provider->setScope($client_scope);
                }
            } else {
                $provider->getRequestToken();
            }
            // redirect user to oauth dialog
            return $app->redirect($provider->getAuthorizeUrl());
        } else if ($action == 'callback') {
            $request_state    = $request->get('state');
            $request_code     = $request->get('code');
            $request_token    = $request->get('oauth_token');
            $request_verifier = $request->get('oauth_verifier');
            // request an access token
            if ($provider->getVersion() == '2.0') {
                $provider->getAccessToken($request_code);
            } else {
                $token = new OAuthToken($request_token);
                $provider->setToken($token);
                $provider->getAccessToken($request_verifier);
            }
            // return the oauth response
            return new Response($provider->getResponse());
        }
    }

    return $app->redirect($app['url_generator']->generate('index'));
})->method('GET|POST')->bind('oauth');

$app->run();
