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

use Socialite\Component\OAuth\OAuthRequest;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthToken;
use Socialite\Component\OAuth\OAuthUtil;

/**
 * The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104]
 * where the Signature Base String is the text and the key is the concatenated values (each first
 * encoded per Parameter Encoding) of the Consumer Secret and Token Secret, separated by an '&'
 * character (ASCII code 38) even if empty.
 *
 * @link http://oauth.net/core/1.0/#anchor16
 */
class OAuthSignatureMethodHMAC extends OAuthSignatureMethod {
    /**
     * (non-PHPdoc)
     * @see Socialite\Component\OAuth\SignatureMethod.OAuthSignatureMethod::name()
     */
    public function name() {
        return "HMAC-SHA1";
    }

    /**
     * (non-PHPdoc)
     * @see Socialite\Component\OAuth\SignatureMethod.OAuthSignatureMethod::build()
     */
    public function build(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token) {
        $base = $request->getBaseSignature();
        $key = OAuthUtil::urlencode($consumer->secret) . '&' . OAuthUtil::urlencode($token->secret);
        if (function_exists('hash_hmac')) {
            $hmac = hash_hmac('sha1', $base, $key, true);
        } else {
            $blocksize = 64;
            $hashfunc  = 'sha1';
            if (strlen($key) > $blocksize) {
                $key = pack('H*', $hashfunc($key));
            }
            $key  = str_pad($key, $blocksize, chr(0x00));
            $ipad = str_repeat(chr(0x36), $blocksize);
            $opad = str_repeat(chr(0x5c), $blocksize);
            $hmac = pack(
                'H*', $hashfunc(
                    ($key^$opad).pack(
                        'H*', $hashfunc(
                            ($key^$ipad).$base_string
                        )
                    )
                )
            );
        }

        return  base64_encode($hmac);;
    }

    /**
     * (non-PHPdoc)
     * @see Socialite\Component\OAuth\SignatureMethod.OAuthSignatureMethod::verify()
     */
    public function verify(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token, $signature) {
        $rawa = OAuthUtil::urldecode($signature);
        $rawb = OAuthUtil::urldecode($this->build($request, $consumer, $token));

        // base64 decode the values
        $decodeda = base64_decode($rawa);
        $decodedb = base64_decode($rawb);

        return rawurlencode($decodeda) == rawurlencode($decodedb);
    }
}
