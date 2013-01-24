<?php

/**
 * This file is part of the Socialite package.
 *
 * (c) Telemundo Digital Media
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socialite\Component\OAuth;

use Socialite\Component\OAuth\OAuthClient;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthToken;
use Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethod;

/**
 * OAuth request.
 *
 * @author Rodolfo Puig <rpuig@7gstudios.com>
 */
class OAuthRequest {
    protected $encoder;
    protected $consumer;
    protected $token;
    protected $version;

    protected $url;
    protected $parameters;
    protected $method;

    protected $state  = 0;

    const STATE_BUILT  = 1;
    const STATE_SIGNED = 2;

    /**
     * Constructor.
     *
     * @param string $url
     * @param string $method
     * @param array  $parameters
     */
    public function __construct($url, array $parameters = NULL, $method = NULL) {
        $this->url = $url;
        if (is_array($parameters)) {
            $this->parameters = $parameters;
        } else {
            $this->parameters = array();
        }
        if ($method !== NULL) {
            $this->method = strtoupper($method);
        } else {
            $this->method = OAuthClient::HTTP_GET;
        }
    }

    /**
     * Return the full request URL.
     *
     * @return string
     */
    public function getRequestUrl() {
        return $this->getNormalizedUrl() . '?' . OAuth::buildHttpQuery($this->parameters);
    }

    /**
     * Returns the normalized OAuth request method.
     *
     * @return string
     */
    public function getNormalizedMethod() {
        return strtoupper($this->method);
    }

    /**
     * Returns the normalized OAuth request url.
     *
     * @return string
     */
    public function getNormalizedUrl() {
        $parts  = parse_url($this->url);
        $scheme = (!empty($parts['scheme'])) ? $parts['scheme'] : 'http';
        $port   = (!empty($parts['port']))   ? $parts['port']   : (($scheme == 'https') ? '443' : '80');
        $host   = (!empty($parts['host']))   ? $parts['host']   : '';
        $path   = (!empty($parts['path']))   ? $parts['path']   : '';
        if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
            $host .= ":{$port}";
        }
        /* TODO: Future update
        if (!empty($parts['user'])) {
            $url .= $parts['user'] . (!empty($parts['pass']) ? ':' : '');
        }
        if (!empty($parts['pass'])) {
            $url .= $parts['pass'] . (!empty($parts['user']) ? '@' : '');
        }
        */

        return sprintf('%s://%s%s', $scheme, $host, $path);
    }

    /**
     * Returns the normalized OAuth request parameters.
     *
     * @return string
     */
    public function getNormalizedParameters() {
        return OAuth::buildHttpQuery($this->getBaseParameters());
    }

    /**
     * Return the base signature string.
     *
     * @return string
     */
    public function getBaseSignature() {
        $signature = array();
        $signature[] = $this->getNormalizedMethod();
        $signature[] = $this->getNormalizedUrl();
        $signature[] = $this->getNormalizedParameters();
        $parameters = array_map(array('Socialite\Component\OAuth\OAuth', 'urlencode'), $signature);

        return implode('&', $parameters);
    }

    /**
     * Returns the base parameters.
     *
     * @return array
     */
    public function getBaseParameters() {
        $parameters = $this->parameters;
        if (isset($parameters['oauth_signature'])) {
            unset($parameters['oauth_signature']);
        }

        return $parameters;
    }

    /**
     * Build the OAuth request parameters.
     *
     * @param  \Socialite\Component\OAuth\OAuthSignatureMethod $encoder
     * @param  \Socialite\Component\OAuth\OAuthConsumer        $consumer
     */
    public function buildParameters(OAuthSignatureMethod $encoder) {
        if (!$this->is(self::STATE_BUILT)) {
            if ($this->getVersion() === 1) {
                // create the oauth parameters
                $parameters = array(
                    'oauth_consumer_key'     => $this->consumer->getKey(),
                    'oauth_signature_method' => $encoder->name(),
                    'oauth_timestamp'        => self::generateTimestamp(),
                    'oauth_nonce'            => self::generateNonce(),
                    'oauth_version'          => $this->version
                );
                // include the token if present
                if ($this->token instanceof OAuthToken) {
                    $parameters['oauth_token'] = $this->token->getToken();
                }
                // merge both the request and oauth parameters
                $this->parameters = array_merge($parameters, $this->parameters);
                $this->encoder    = $encoder;
                // mark the request parameters as built
                $this->state |= self::STATE_BUILT;
            } elseif ($this->getVersion() === 2) {
                // create the oauth parameters
                $parameters = array(
                    'client_id'     => $this->consumer->getKey(),
                    'client_secret' => $this->consumer->getSecret()
                );
                // merge both the request and oauth parameters
                $this->parameters = array_merge($parameters, $this->parameters);
                // mark the request parameters as built
                $this->state |= self::STATE_BUILT;
            }
        } else {
            // TODO: log this condition
        }
    }

    /**
     * Generate the OAuth request signature.
     */
    public function signRequest() {
        if ($this->is(self::STATE_BUILT) && !$this->is(self::STATE_SIGNED)) {
            if ($this->getVersion() === 1) {
                // create the oauth signature
                $signature = $this->encoder->build($this, $this->consumer, $this->token);
                if ($signature !== NULL) {
                    $this->parameters['oauth_signature'] = $signature;
                }
                // mark the request as signed
                $this->state |= self::STATE_SIGNED;
            }
        } else {
            // TODO: log this condition
        }
    }

    /**
     * @return \Socialite\Component\OAuth\OAuthSignatureMethod
     */
    public function getEncoder() {
        return $this->encoder;
    }

    /**
     * @param \Socialite\Component\OAuth\OAuthSignatureMethod $encoder
     */
    public function setEncoder(OAuthSignatureMethod $encoder) {
        $this->encoder = $encoder;
    }

    /**
     * @return \Socialite\Component\OAuth\OAuthConsumer
     */
    public function getConsumer() {
        return $this->consumer;
    }

    /**
     * @param \Socialite\Component\OAuth\OAuthConsumer $consumer
     */
    public function setConsumer(OAuthConsumer $consumer) {
        $this->consumer = $consumer;
    }

    /**
     * @return \Socialite\Component\OAuth\OAuthToken
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param \Socialite\Component\OAuth\OAuthToken $token
     */
    public function setToken(OAuthToken $token) {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getVersion() {
        return (int)$this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version) {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Check if the request is in a certain state.
     *
     * @param int $state
     */
    public function is($state) {
        return ($this->state & $state);
    }

    /**
     * Generate a UNIX timestamp.
     *
     * @return int
     */
    static public function generateTimestamp() {
        return time();
    }

    /**
     * Generate a NONCE value.
     *
     * @return string
     */
    static public function generateNonce() {
        $time = microtime(TRUE);
        $rand = uniqid('socialite_', true);

        return hash('md5', $time . $rand);
    }

    /**
     * Generate a request from globals.
     *
     * @param  string $url
     * @return \Socialite\Component\OAuth\OAuthRequest
     */
    static public function generateFromGlobals($url) {
        $validkeys  = array('oauth_callback', 'oauth_consumer_key', 'oauth_nonce', 'oauth_signature_method', 'oauth_timestamp', 'oauth_version');
        $parameters = array();
        foreach($validkeys as $key) {
            if (isset($_REQUEST[$key])) {
                $parameters[$key] = $_REQUEST[$key];
            }
        }

        return new OAuthRequest($url, $parameters);
    }
}