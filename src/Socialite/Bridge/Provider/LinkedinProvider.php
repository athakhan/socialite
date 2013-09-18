<?php

/**
 * This file is part of the Socialite package.
 *
 * (c) Telemundo Digital Media
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socialite\Bridge\Provider;

use Socialite\Bridge\Provider\Base\BaseProvider;
use Socialite\Component\OAuth\OAuth;
use Socialite\Component\OAuth\OAuthClient;
use Socialite\Component\OAuth\OAuthRequest;

/**
 * Linkedin provider.
 *
 * @author Rodolfo Puig <rpuig@7gstudios.com>
 */
class LinkedinProvider extends BaseProvider {
    protected $oauth_request_token_url = 'https://api.linkedin.com/uas/oauth/requestToken';
    protected $oauth_access_token_url  = 'https://api.linkedin.com/uas/oauth/accessToken';
    protected $oauth_authorize_url     = 'https://api.linkedin.com/uas/oauth/authenticate';
    protected $oauth_version           = '1.0';

    /**
     * (non-PHPdoc)
     * @see \Socialite\Bridge\Provider\BaseProvider::getRequestToken()
     */
    public function getRequestToken(array $params = null) {
        $url = $this->getNormalizedUrl($this->oauth_request_token_url);
        // add the callback to the request parameters
        $defaults = array();
        $defaults['oauth_callback'] = $this->callback;
        $parameters = $params ? array_merge($defaults, $params) : $defaults;
        // create the request object
        $request = new OAuthRequest($url, $parameters, OAuthClient::HTTP_POST);
        $request->setVersion($this->oauth_version);
        $request->setConsumer($this->consumer);
        // execute the request
        $client = new OAuthClient($request);
        $client->execute();
        // store the response
        $this->response = $client->getResponse();
    }

    /**
     * (non-PHPdoc)
     * @see \Socialite\Bridge\Provider\BaseProvider::getAccessToken()
     */
    public function getAccessToken(array $params = null) {
        $url = $this->getNormalizedUrl($this->oauth_access_token_url);
        // create the request object
        $request = new OAuthRequest($url, $params, OAuthClient::HTTP_POST);
        $request->setConsumer($this->consumer);
        $request->setToken($this->token);
        $request->setVersion($this->oauth_version);
        // execute the request
        $client = new OAuthClient($request);
        $client->execute();
        // store the response
        $this->response = $client->getResponse();
    }

    /**
     * (non-PHPdoc)
     * @see \Socialite\Bridge\Provider\BaseProvider::getAuthorizeUrl()
     */
    public function getAuthorizeUrl(array $params = null) {
        $url = $this->getNormalizedUrl($this->oauth_authorize_url);

        return $this->getParametizedUrl($url, $this->response);
    }
}
