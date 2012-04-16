<?php

/**
 * Copyright (c) Telemundo Digital Media
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Socialite\Bridge\Twitter;


use Socialite\Component\OAuth\Exception\OAuthNetworkException;

use Socialite\Component\OAuth\OAuthRequest;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthToken;
use Socialite\Component\OAuth\OAuthUtil;
use Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethodHMAC;

/**
 * Twitter request helper class
 */
class Request {
    protected $encoder;
    protected $consumer;
    protected $token;
    protected $request;

    const OAUTH_USERAGENT         = 'Socialite/1.0';
    const OAUTH_ACCESS_TOKEN_URL  = 'https://api.twitter.com/oauth/access_token';
    const OAUTH_AUTHENTICATE_URL  = 'https://api.twitter.com/oauth/authenticate';
    const OAUTH_AUTHORIZE_URL     = 'https://api.twitter.com/oauth/authorize';
    const OAUTH_REQUEST_TOKEN_URL = 'https://api.twitter.com/oauth/request_token';

    const TYPE_JSON   = 'json';
    const TYPE_XML    = 'xml';
    const TYPE_RAW    = 'raw';

    const HTTP_DELETE = 'DELETE';
    const HTTP_GET    = 'GET';
    const HTTP_POST   = 'POST';
    const HTTP_PUT    = 'PUT';

    /**
     * Constructor
     *
     * @param OAuthConsumer $consumer
     * @param OAuthToken    $token
     */
    public function __construct(OAuthConsumer $consumer, OAuthToken $token = NULL) {
        $this->encoder = new OAuthSignatureMethodHMAC();
        $this->consumer = $consumer;
        if ($token instanceof OAuthToken) {
            $this->token = $token;
        }
    }

    /**
     * Build the OAuth request.
     *
     * @param  string $url
     * @param  string $method
     * @param  array  $parameters
     * @return \Socialite\Component\OAuth\OAuthRequest
     */
    public function build($url, $method, array $parameters = NULL) {
        $this->request = new OAuthRequest($url, $method, $parameters);
        $this->request->buildParameters($this->encoder, $this->consumer, $this->token);
        $this->request->signRequest();
    }

    /**
     * Execute the HTTP request.
     *
     * @param  array $curlOpts
     * @return string
     */
    public function execute($data = NULL, array $curlOpts = NULL) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, self::OAUTH_USERAGENT);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        // apply any method specific settings
        switch ($this->request->getMethod()) {
            case self::HTTP_POST:
                curl_setopt($curl, CURLOPT_POST, TRUE);
                if (!empty($data)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
        }
        // apply the cURL configuration overrides
        if (is_array($curlOpts)) {
            curl_setopt_array($curl, $curlOpts);
        }
        // execute the cURL connection
        curl_setopt($curl, CURLOPT_URL, $this->request->getRequestUrl());
        $response = curl_exec($curl);
        if ($response === FALSE) {
            throw new OAuthNetworkException('cURL: ' . curl_error($curl));
        }
        curl_close ($curl);

        return $response;
    }
}