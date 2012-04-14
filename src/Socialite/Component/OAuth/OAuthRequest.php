<?php

/**
 * Copyright (c) Telemundo Digital Media
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the 'Software'), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Socialite\Component\OAuth;

/**
 *
 */
use Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethod;

class OAuthRequest {
    protected $realm;
    protected $url;
    protected $method;
    protected $parameters;
    protected $headers;
    protected $body;
    protected $encoder;
    protected $consumer;
    protected $token;
    protected $state = 0;

    const IS_BUILT  = 1;
    const IS_SIGNED = 2;

    const CLIENT_UA = 'Socialite/1.0';

    public function getRealm() { return $this->realm; }
    public function getUrl() { return $this->url; }
    public function getMethod() { return $this->method; }
    public function getHeaders() { return $this->headers; }
    public function getBody() { return $this->body; }
    public function getEncoder() { return $this->encoder; }
    public function getConsumer() { return $this->consumer; }
    public function getToken() { return $this->token; }

    /**
     * Constructor
     *
     * @param string $url
     * @param string $method
     * @param array  $parameters
     */
    public function __construct($url = NULL, $method = NULL, array $parameters = NULL) {
        // process the incoming request data when no url is present
        if (empty($url)) {
            if (strpos($_SERVER['REQUEST_URI'], "://") !== FALSE) {
                $this->url = $_SERVER['REQUEST_URI'];
            } else {
                $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $this->url = sprintf('%s://%s%s', $scheme, $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);
            }
            // parse incoming headers
            //$this->headers = OAuthUtil::parseHeaders();
        } else {
            $this->url = $url;
        }
        // set the request method
        if ($method === NULL) {
            if (isset($_SERVER['REQUEST_METHOD'])) {
                $this->method = $_SERVER['REQUEST_METHOD'];
            } else {
                $this->method = 'GET';
            }
        } else {
            $this->method = strtoupper($method);
        }
        // set the reuqest parameters
        if (is_array($parameters)) {
            $this->parameters = $parameters;
        } else {
            $this->parameters = array();
        }
    }

    /**
     * Return the base signature string.
     *
     * @return string
     */
    public function getBaseSignature() {
        $signature = array();
        $signature[] = $this->method;
        $signature[] = $this->getBaseUrl();
        $signature[] = $this->getNormalizedParameters();
        $parameters = array_map(array('Socialite\Component\OAuth\OAuthUtil', 'urlencode'), $signature);

        return implode('&', $parameters);
    }

    /**
     * Return the base request url.
     *
     * @return string
     */
    public function getBaseUrl() {
        $parts = parse_url($this->url);
        if (!empty($parts['scheme'])) {
            $url  = $parts['scheme'] . '://';
        }
        if (!empty($parts['user'])) {
            $url .= $parts['user'] . (!empty($parts['pass']) ? ':' : '');
        }
        if (!empty($parts['pass'])) {
            $url .= $parts['pass'] . (!empty($parts['user']) ? '@' : '');
        }
        if (!empty($parts['host'])) {
            $url .= $parts['host'];
        }
        if (!empty($parts['path'])) {
            $url .= $parts['path'];
        }

        return $url;
    }

    /**
     * Return the full request URL.
     *
     * @return string
     */
    public function getRequestUrl() {
        return $this->getBaseUrl() . '?' . $this->getNormalizedParameters();
    }

    /**
     * Return the encoded, sorted parameters.
     *
     * @return string
     */
    public function getNormalizedParameters() {
        $normalized = array();
        $parameters = array_map(array('Socialite\Component\OAuth\OAuthUtil', 'urlencode'), $this->parameters);
        uksort($parameters, 'strcmp');
        foreach ($parameters as $key => $value) {
            $normalized[] = $key . '=' . $value;
        }

        return implode('&', $normalized);
    }

    /**
     * Build the OAuth request parameters.
     *
     * @param  OAuthSignatureMethod $encoder
     * @param  OAuthConsumer        $consumer
     * @param  OAuthToken           $token
     */
    public function buildParameters(OAuthSignatureMethod $encoder, OAuthConsumer $consumer, OAuthToken $token = NULL) {
        if (!$this->is(OAuthRequest::IS_BUILT)) {
            // mark the parameters as built
            $this->state |= OAuthRequest::IS_BUILT;
            // create the oauth parameters
            $parameters = array(
                'oauth_signature_method' => $encoder->name(),
                'oauth_timestamp'        => OAuthRequest::generateTimestamp(),
                'oauth_nonce'            => OAuthRequest::generateNonce(),
                'oauth_version'          => '1.0',
                'oauth_consumer_key'     => $consumer->key
            );
            // include the user token if passed
            if ($token instanceof OAuthToken) {
                $parameters['oauth_token'] = $token->token;
            }
            // merge both the request and oauth parameters
            $this->parameters = array_merge($parameters, $this->parameters);
            // store the passed parameters
            $this->encoder  = $encoder;
            $this->consumer = $consumer;
            $this->token    = $token;
        }
    }

    /**
     * Generate the OAuth request signature.
     */
    public function signRequest() {
        if (!$this->is(OAuthRequest::IS_SIGNED)) {
            // mark the request as signed
            $this->state |= OAuthRequest::IS_SIGNED;
            // create the oauth signature
            $signature = $this->encoder->build($this, $this->consumer, $this->token);
            if ($signature !== NULL) {
                $this->parameters['oauth_signature'] = $signature;
            }
        }
    }

    /**
     * Check if the request is in a certain state.
     *
     * @param int $state
     */
    protected function is($state) {
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
        $time = microtime();
        $rand = uniqid('nonce_', true);

        return md5($time . $rand);
    }
}