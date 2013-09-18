<?php

/**
 * This file is part of the Socialite package.
 *
 * (c) Telemundo Digital Media
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socialite\Bridge\Provider\Base;

use Socialite\Component\OAuth\OAuth;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthToken;

/**
 * Base provider.
 *
 * @author Rodolfo Puig <rpuig@7gstudios.com>
 */
abstract class BaseProvider {
    protected $consumer;
    protected $token;
    protected $scope;
    protected $callback;
    protected $response;

    protected $oauth_request_token_url = '';
    protected $oauth_access_token_url  = '';
    protected $oauth_authenticate_url  = '';
    protected $oauth_authorize_url     = '';
    protected $oauth_scope_separator   = ' ';
    protected $oauth_version           = '1.0';

    /**
     * Constructor.
     *
     * @param \Socialite\Component\OAuth\OAuthConsumer $consumer
     * @param string                                   $callback
     */
    public function __construct(OAuthConsumer $consumer, $callback = 'oob') {
        $this->consumer = $consumer;
        $this->callback = $callback;
        $this->scope    = array();
    }

    /**
     * Sets the request consumer.
     *
     * #param \Socialite\Component\OAuth\OAuthConsumer $consumer
     */
    public function setConsumer(OAuthConsumer $consumer) {
        $this->consumer = $consumer;
    }

    /**
     * Returns the request consumer.
     *
     * @return \Socialite\Component\OAuth\OAuthConsumer
     */
    public function getConsumer() {
        return $this->consumer;
    }

    /**
     * Sets the request token.
     *
     * #param \Socialite\Component\OAuth\OAuthToken $token
     */
    public function setToken(OAuthToken $token) {
        $this->token = $token;
    }

    /**
     * Returns the request token.
     *
     * @return \Socialite\Component\OAuth\OAuthToken
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Sets the callback URL.
     *
     * #param string $callback
     */
    public function setCallback($callback) {
        $this->callback = $callback;
    }

    /**
     * Returns the callback URL.
     *
     * @return string
     */
    public function getCallback() {
        return $this->callback;
    }

    /**
     * Sets the request scope.
     *
     * @param array $scope
     */
    public function setScope(array $scope) {
        $this->scope = $scope;
    }

    /**
     * Returns the request scope.
     *
     * @return array
     */
    public function getScope() {
        return $this->scope;
    }

    /**
     * Returns the stored server response.
     *
     * @return string
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Returns the OAuth version number.
     *
     * @return int
     */
    public function getVersion() {
        return $this->oauth_version;
    }

    /**
     * Returns the normalized URL.
     *
     * @param  string $url
     * @return string
     */
    public function getNormalizedUrl($url) {
        $parameters = array(
            '{REDIRECT_URI}' => $this->callback,
            '{CLIENT_ID}'    => $this->consumer->getKey(),
            '{SCOPE}'        => implode($this->oauth_scope_separator, $this->getScope()),
            '{STATE}'        => md5(microtime(true))
        );
        foreach ($parameters as $name => $value) {
            $url = str_replace($name, OAuth::urlencode($value), $url);
        }

        return $url;
    }

    /**
     * Returns the parametized URL.
     *
     * @param  string $url
     * @param  mixed  $params
     * @return string
     */
    public function getParametizedUrl($url, $params = null) {
        if ($params !== null) {
            $sep = (strstr($url, '?') === false) ? '?' : '&';
            if (is_array($params)) {
                $url .= $sep . http_build_query($params);
            } elseif (is_scalar($params)) {
                $url .= $sep . $params;
            }
        }

        return $url;
    }

    /**
     * Generates a request token.
     *
     * @param array $params
     */
    abstract public function getRequestToken(array $params = null);

    /**
     * Generates an access token.
     *
     * @param string $verifier
     */
    abstract public function getAccessToken($verifier);

    /**
     * Returns the authorization dialog URL.
     *
     * @param  array $params
     * @return string
     */
    abstract public function getAuthorizeUrl(array $params = null);
}
