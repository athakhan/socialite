<?php

/**
 * This file is part of the Socialite package.
 *
 * (c) Telemundo Digital Media
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socialite\Component\OAuth\SignatureMethod;

use Socialite\Component\OAuth\OAuth;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthRequest;
use Socialite\Component\OAuth\OAuthToken;

/**
 * The PLAINTEXT method does not provide any security protection and SHOULD only be used
 * over a secure channel such as HTTPS. It does not use the Signature Base String.
 *
 * @link http://oauth.net/core/1.0/#anchor22
 */
class OAuthSignatureMethodPLAIN extends OAuthSignatureMethod {
    /**
     * (non-PHPdoc)
     * @see \Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethod::name()
     */
    public function name() {
        return 'PLAINTEXT';
    }

    /**
     * (non-PHPdoc)
     * @see \Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethod::build()
     */
    public function build(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token = null) {
        $parts   = array(OAuth::urlencode($consumer->getSecret()));
        $parts[] = ($token instanceof OAuthToken) ? OAuth::urlencode($token->getSecret()) : '';

        return join('&', $parts);
    }

    /**
     * (non-PHPdoc)
     * @see \Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethod::verify()
     */
    public function verify(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token = null, $signature) {
        $rawa = OAuth::urldecode($signature);
        $rawb = OAuth::urldecode($this->build($request, $consumer, $token));

        return rawurlencode($rawa) == rawurlencode($rawb);
    }
}
