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

namespace Socialite\Bridge\Twitter\Client;

use Socialite\Bridge\Twitter\Request;
use Socialite\Component\OAuth\OAuthRequest;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthToken;

/**
 * Twitter REST API client
 */
class RestClient {
    protected $consumer;
    protected $token;
    protected $curl_opts;
    protected $content_type;

    const RESTAPI_ENDPOINT = 'https://api.twitter.com/1/s';

    const GET_FAVORITES                = 'favorites';
    const GET_SEARCH                   = 'search';
    const GET_STATUSES_MENTIONS        = 'statuses/mentions';
    const GET_STATUSES_HOME_TIMELINE   = 'statuses/home_timeline';
    const GET_STATUSES_USER_TIMELINE   = 'statuses/user_timeline';
    const GET_STATUSES_RETWEET_BY_ME   = 'statuses/retweeted_by_me';
    const GET_STATUSES_RETWEET_TO_ME   = 'statuses/retweeted_to_me';
    const GET_STATUSES_RETWEET_OF_ME   = 'statuses/retweets_of_me';
    const GET_STATUSES_RETWEET_TO_USER = 'statuses/retweeted_to_user';
    const GET_STATUSES_RETWEET_BY_USER = 'statuses/retweeted_by_user';
    const GET_USERS_LOOKUP             = 'users/lookup';
    const GET_USERS_SEARCH             = 'users/search';
    const GET_USERS_SHOW               = 'users/show';
    const GET_USERS_CONTRIBUTEES       = 'users/contributees';
    const GET_USERS_CONTRIBUTORS       = 'users/contributors';
    const GET_USERS_SUGGESTIONS        = 'users/suggestions';

    /**
     * Constructor
     *
     * @param string $consumer_key
     * @param string $consumer_secret
     * @param string $user_token
     * @param string $user_secret
     * @param array  $curl_opts
     */
    public function __construct($consumer_key, $consumer_secret, $user_token = NULL, $user_secret = NULL, array $curl_opts = NULL) {
        $this->consumer     = new OAuthConsumer($consumer_key, $consumer_secret);
        $this->token        = new OAuthToken($user_token, $user_secret);
        $this->curl_opts    = $curl_opts;
        $this->content_type = Request::TYPE_JSON;
    }

    /**
     * Create the request URL.
     *
     * @param  string $url
     * @param  string $contentType
     * @return string
     */
    public function createUrl($url, $contentType = NULL) {
        if ($contentType !== NULL) {
            $this->content_type = $contentType;
        }

        return $this->prepareUrl($url);
    }

    /**
     * Perform a GET request.
     *
     * @param  string $url
     * @param  array  $parameters
     * @return mixed
     */
    public function get($url, array $parameters = NULL) {
        $request = new Request($this->consumer, $this->token);
        $request->build($url, Request::HTTP_GET, $parameters);
        $result = $request->execute(NULL, $this->curl_opts);
        // parse the content as requested
        switch ($this->content_type) {
            case Request::TYPE_XML:
                $result = simplexml_load_string($result);
                break;
            case Request::TYPE_JSON:
                $result = json_decode($result);
                break;
        }

        return $result;
    }

    /**
     * Prepare the request URL.
     *
     * @param  string $url
     * @return string
     */
    protected function prepareUrl($url) {
        switch ($this->content_type) {
            case Request::TYPE_XML:
                $extension = '.xml';
                break;
            case Request::TYPE_JSON:
            default:
                $extension = '.json';
        }

        return RestClient::RESTAPI_ENDPOINT . $url . $extension;
    }
}
