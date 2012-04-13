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

namespace Socialite\Component\OAuth\SignatureMethod;

/**
 * Abstract class for implementing a Signature Method.
 *
 * @link http://oauth.net/core/1.0/#signing_process
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
    abstract public function build(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token);

    /**
     * Check if the request signature corresponds to the one calculated for the request.
     *
     * @param  OAuthRequest  $request
     * @param  OAuthConsumer $consumer
     * @param  OAuthToken    $token
     * @param  string        $signature
     * @return bool
     */
    abstract public function verify(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token, $signature);
}
