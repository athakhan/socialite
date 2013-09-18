<?php
/**
 * This file is part of the Socialite package.
 *
 * Copyright (c) Telemundo Digital Media
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Socialite\Component\OAuth\SignatureMethod;

use Socialite\Component\OAuth\OAuth;
use Socialite\Component\OAuth\OAuthConsumer;
use Socialite\Component\OAuth\OAuthRequest;
use Socialite\Component\OAuth\OAuthToken;

/**
 * The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104]
 * where the Signature Base String is the text and the key is the concatenated values (each first
 * encoded per Parameter Encoding) of the Consumer Secret and Token Secret, separated by an '&'
 * character (ASCII code 38) even if empty.
 *
 * @link http://oauth.net/core/1.0/#anchor16
 *
 * @author Rodolfo Puig <rodolfo@puig.io>
 */
class OAuthSignatureMethodHMAC extends OAuthSignatureMethod {
    /**
     * (non-PHPdoc)
     * @see \Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethod::name()
     */
    public function name() {
        return "HMAC-SHA1";
    }

    /**
     * (non-PHPdoc)
     * @see \Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethod::build()
     */
    public function build(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token = null) {
        $parts   = array(OAuth::urlencode($consumer->getSecret()));
        $parts[] = ($token instanceof OAuthToken) ? OAuth::urlencode($token->getSecret()) : '';
        $base    = $request->getBaseSignature();
        $key     = join('&', $parts);

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

        return base64_encode($hmac);;
    }

    /**
     * (non-PHPdoc)
     * @see \Socialite\Component\OAuth\SignatureMethod\OAuthSignatureMethod::verify()
     */
    public function verify(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token = null, $signature) {
        $rawa = OAuth::urldecode($signature);
        $rawb = OAuth::urldecode($this->build($request, $consumer, $token));

        // base64 decode the values
        $decodeda = base64_decode($rawa);
        $decodedb = base64_decode($rawb);

        return rawurlencode($decodeda) == rawurlencode($decodedb);
    }
}
