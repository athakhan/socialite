<?php
/**
 * This file is part of the Socialite package.
 *
 * Copyright (c) Telemundo Digital Media
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Socialite\Component\OAuth;

/**
 * OAuth base.
 *
 * @author Rodolfo Puig <rodolfo@puig.io>
 */
class OAuth {
    /**
     * Encode the input as specified by RFC 3986.
     *
     * Input can be a scalar or array value. When passing an array, the
     * values will be encoded recursively and the array will be returned.
     *
     * @param  mixed $value
     * @return mixed
     */
    static public function urlencode($value) {
        if (is_array($value)) {
            return array_map(array('Socialite\Component\OAuth\OAuth', 'urlencode'), $value);
        } else if (is_scalar($value)) {
            return rawurlencode($value);
        } else {
            return '';
        }
    }

    /**
     * Decode the input as specified by RFC 3986.
     *
     * Input can be a scalar or array value. When passing an array, the
     * values will be encoded recursively and the array will be returned.
     *
     * @param  mixed $value
     * @return mixed
     */
    static public function urldecode($value) {
        if (is_array($value)) {
            return array_map(array('Socialite\Component\OAuth\OAuth', 'urldecode'), $value);
        } else if (is_scalar($value)) {
            return rawurldecode($value);
        } else {
            return '';
        }
    }

    /**
     * Return the encoded, sorted parameters.
     *
     * @param  array  $parameters
     * @return string
     */
    static public function buildHttpQuery(array $parameters) {
        $normalized = array();
        $parameters = array_map(array('Socialite\Component\OAuth\OAuth', 'urlencode'), $parameters);
        uksort($parameters, 'strcmp');
        foreach ($parameters as $key => $value) {
            $normalized[] = $key . '=' . $value;
        }

        return implode('&', $normalized);
    }
}
