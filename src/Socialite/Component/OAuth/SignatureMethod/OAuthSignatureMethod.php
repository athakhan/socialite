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

use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthRequest;
use Socialite\Component\OAuth\OAuthToken;

/**
 * Abstract class for implementing a Signature Method.
 *
 * @link http://oauth.net/core/1.0/#signing_process
 *
 * @author Rodolfo Puig <rpuig@7gstudios.com>
 */
abstract class OAuthSignatureMethod {
    /**
     * Return the name of this signature.
     *
     * @return string
     */
    abstract public function name();

    /**
     * Return the signature for the given request.
     *
     * Note: The output of this function MUST NOT be urlencoded. The encoding is handled in
     * OAuthRequest when the final request is serialized.
     *
     * @param  OAuthRequest  $request
     * @param  OAuthConsumer $consumer
     * @param  OAuthToken    $token
     * @return string
     */
    abstract public function build(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token = NULL);

    /**
     * Check if the request signature corresponds to the one calculated for the request.
     *
     * @param  OAuthRequest  $request
     * @param  OAuthConsumer $consumer
     * @param  OAuthToken    $token
     * @param  string        $signature
     * @return bool
     */
    abstract public function verify(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token = NULL, $signature);
}
