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

namespace Socialite\Component\OAuth;

/**
 * The PLAINTEXT method does not provide any security protection and SHOULD only be used
 * over a secure channel such as HTTPS. It does not use the Signature Base String.
 *
 * @link http://oauth.net/core/1.0/#anchor22
 */
class OAuthSignatureMethod_PLAINTEXT extends OAuthSignatureMethod {
    /**
     * (non-PHPdoc)
     * @see Socialite\Component\OAuth.OAuthSignatureMethod::name()
     */
    public function name() {
        return 'PLAINTEXT';
    }

    /**
     * (non-PHPdoc)
     * @see Socialite\Component\OAuth.OAuthSignatureMethod::build()
     */
    public function build(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token) {
        return OAuthUtil::urlencode($consumer->key) . '&' . OAuthUtil::urlencode($consumer->secret);
    }

    /**
     * (non-PHPdoc)
     * @see Socialite\Component\OAuth.OAuthSignatureMethod::verify()
     */
    public function verify(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token, $signature) {
        $rawa = OAuthUtil::urldecode($signature);
        $rawb = OAuthUtil::urldecode($this->build($request, $consumer, $token));

        return rawurlencode($rawa) == rawurlencode($rawb);
    }
}