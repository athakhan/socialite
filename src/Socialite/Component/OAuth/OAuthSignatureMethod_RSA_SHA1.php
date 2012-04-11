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
 * The RSA-SHA1 signature method uses the RSASSA-PKCS1-v1_5 signature algorithm as defined in
 * [RFC3447] section 8.2 (more simply known as PKCS#1), using SHA-1 as the hash function for
 * EMSA-PKCS1-v1_5. It is assumed that the Consumer has provided its RSA public key in a verified
 * way to the Service Provider, in a manner which is beyond the scope of this specification.
 *
 * @link http://oauth.net/core/1.0/#anchor19
 */
class OAuthSignatureMethod_RSA_SHA1 extends OAuthSignatureMethod {
    /**
     * Fetch the public certificate key for the signature.
     *
     * Note: Up to the SP to implement this lookup of keys. Possible ideas are:
     * (1) do a lookup in a table of trusted certs keyed off of consumer
     * (2) fetch via http using a url provided by the requester
     * (3) some sort of specific discovery code based on request
     *
     * @param  OAuthRequest $request
     * @return string
     */
    abstract protected function fetch_public_cert(OAuthRequest $request);

    /**
     * Fetch the private certificate key for the signature
     *
     * Note: Up to the SP to implement this lookup of keys. Possible ideas are:
     * (1) do a lookup in a table of trusted certs keyed off of consumer
     *
     * @param  OAuthRequest $request
     * @return string
     */
    abstract protected function fetch_private_cert(OAuthRequest $request);

    /**
     * (non-PHPdoc)
     * @see Socialite\Component\OAuth.OAuthSignatureMethod::name()
     */
    public function name() {
        return "RSA-SHA1";
    }

    /**
     * (non-PHPdoc)
     * @see Socialite\Component\OAuth.OAuthSignatureMethod::build()
     */
    public function build(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token) {
        // Fetch the private key cert based on the request
        $cert = $this->fetch_private_cert($request);

        // Pull the private key ID from the certificate
        $privatekeyid = openssl_get_privatekey($cert);

        // Sign using the key
        $ok = openssl_sign($base_string, $signature, $privatekeyid);

        // Release the key resource
        openssl_free_key($privatekeyid);

        return OAuthUtil::urlencode(base64_encode($signature));
    }

    /**
     * (non-PHPdoc)
     * @see Socialite\Component\OAuth.OAuthSignatureMethod::verify()
     */
    public function verify(OAuthRequest $request, OAuthConsumer $consumer, OAuthToken $token, $signature) {
        $decoded_sig = base64_decode(OAuthUtil::urldecode($signature));

        // Fetch the public key cert based on the request
        $cert = $this->fetch_public_cert($request);

        // Pull the public key ID from the certificate
        $publickeyid = openssl_get_publickey($cert);

        // Check the computed signature against the one passed in the query
        $ok = openssl_verify($base_string, $decoded_signature, $publickeyid);

        // Release the key resource
        openssl_free_key($publickeyid);

        return $ok === 1;
    }
}
