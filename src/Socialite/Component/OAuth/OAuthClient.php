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

use Socialite\Component\OAuth\OAuth;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthRequest;
use Socialite\Component\OAuth\Exception\OAuthException;
use Socialite\Component\OAuth\Exception\OAuthNetworkException;
use Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethodHMAC;

/**
 * OAuth client.
 *
 * @author Rodolfo Puig <rpuig@7gstudios.com>
 */
class OAuthClient {
    protected $encoder;
    protected $request;
    protected $response;

    const TYPE_JSON   = 'json';
    const TYPE_XML    = 'xml';

    const HTTP_DELETE = 'DELETE';
    const HTTP_GET    = 'GET';
    const HTTP_POST   = 'POST';
    const HTTP_PUT    = 'PUT';

    const USERAGENT   = 'Socialite/1.1 (+https://github.com/telemundo/socialite)';

    /**
     * Constructor.
     *
     * @param OAuthConsumer $consumer
     */
    public function __construct(OAuthRequest &$request) {
        $this->encoder = new OAuthSignatureMethodHMAC();
        $this->request = $request;
        $this->request->buildParameters($this->encoder);
        $this->request->signRequest();
    }

    /**
     * Execute the HTTP request.
     *
     * @param  string $data
     */
    public function execute(array $curl_opts = null) {
        $curl = curl_init($this->request->getNormalizedUrl());
        curl_setopt($curl, CURLOPT_USERAGENT, self::USERAGENT);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        // prepare request data
        switch ($this->request->getMethod()) {
            case 'POST':
                $parameters = $this->request->getParameters();
                curl_setopt($curl, CURLOPT_POST, count($parameters));
                curl_setopt($curl, CURLOPT_POSTFIELDS, OAuth::buildHttpQuery($parameters));
                break;
            case 'GET':
                curl_setopt($curl, CURLOPT_URL, $this->request->getRequestUrl());
                break;
        }
        // apply any override parameters
        if (is_array($curl_opts)) {
            curl_setopt_array($curl, $curl_opts);
        }
        // execute the request
        $response = curl_exec($curl);
        if ($response === false) {
            throw new OAuthNetworkException('OAuth Request: ' . curl_error($curl));
        }
        // check for errors
        $info = curl_getinfo($curl);
        if ($info['http_code'] !== 200) {
            throw new OAuthException('OAuth Response: ' . $response);
        }
        // finalize the request
        curl_close($curl);
        // store the response
        $this->response = $response;
    }

    /**
     * Returns the encoder.
     *
     * @return \Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethodHMAC
     */
    public function getEncoder() {
        return $this->encoder;
    }

    /**
     * Returns the consumer.
     *
     * @return \Socialite\Component\OAuth\OAuthConsumer
     */
    public function getConsumer() {
        return $this->consumer;
    }

    /**
     * Returns the request.
     *
     * @return \Socialite\Component\OAuth\OAuthRequest
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Returns the request response.
     *
     * @return string
     */
    public function getResponse() {
        return $this->response;
    }
}
