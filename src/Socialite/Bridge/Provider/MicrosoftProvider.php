<?php
/**
 * This file is part of the Socialite package.
 *
 * Copyright (c) Telemundo Digital Media
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Socialite\Bridge\Provider;

use Socialite\Bridge\Provider\Base\BaseProvider;
use Socialite\Component\OAuth\OAuth;
use Socialite\Component\OAuth\OAuthClient;
use Socialite\Component\OAuth\OAuthRequest;

/**
 * Microsoft provider.
 *
 * @author Rodolfo Puig <rodolfo@puig.io>
 */
class MicrosoftProvider extends BaseProvider {
    protected $oauth_access_token_url  = 'https://login.live.com/oauth20_token.srf';
    protected $oauth_authorize_url     = 'https://login.live.com/oauth20_authorize.srf?response_type=code&client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&scope={SCOPE}&state={STATE}';
    protected $oauth_version           = '2.0';

    /**
     * (non-PHPdoc)
     * @see \Socialite\Bridge\Provider\BaseProvider::getRequestToken()
     */
    public function getRequestToken(array $params = null) {
        return;
    }

    /**
     * (non-PHPdoc)
     * @see \Socialite\Bridge\Provider\BaseProvider::getAccessToken()
     */
    public function getAccessToken($verifier) {
        $url = $this->getNormalizedUrl($this->oauth_access_token_url);
        // build the oauth request parameters
        $params = array('code' => $verifier, 'redirect_uri' => $this->getCallback(), 'grant_type' => 'authorization_code');
        // create the request object
        $request = new OAuthRequest($url, $params, OAuthClient::HTTP_POST);
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
     * @see \Socialite\Bridge\Provider\Base\BaseProvider::getAuthorizeUrl()
     */
    public function getAuthorizeUrl(array $params = null) {
        $url = $this->getNormalizedUrl($this->oauth_authorize_url);

        return $this->getParametizedUrl($url, $params);
    }
}
