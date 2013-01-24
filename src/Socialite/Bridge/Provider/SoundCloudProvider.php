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
 * Soundcloud provider.
 *
 * @author Rodolfo Puig <rpuig@7gstudios.com>
 */
class SoundcloudProvider extends BaseProvider {
    protected $oauth_access_token_url  = 'https://api.soundcloud.com/oauth2/token';
    protected $oauth_authorize_url     = 'https://soundcloud.com/connect?response_type=code&client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&scope={SCOPE}&state={STATE}';
    protected $oauth_version           = '2.0';

    /**
     * (non-PHPdoc)
     * @see \Socialite\Bridge\Provider\BaseProvider::getRequestToken()
     */
    public function getRequestToken(array $params = NULL) {
        return;
    }

    /**
     * (non-PHPdoc)
     * @see \Socialite\Bridge\Provider\BaseProvider::getAccessToken()
     */
    public function getAccessToken(array $params = NULL) {
        $url = $this->getNormalizedUrl($this->oauth_access_token_url);
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
    public function getAuthorizeUrl(array $params = NULL) {
        $url = $this->getNormalizedUrl($this->oauth_authorize_url);

        return $this->getParametizedUrl($url, $params);
    }
}
