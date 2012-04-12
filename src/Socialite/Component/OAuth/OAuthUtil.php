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
 * Utility class used by the OAuth components
 */
class OAuthUtil {
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
            return array_map(array('OAuthUtil', 'urlencode'), $value);
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
            return array_map(array('OAuthUtil', 'urldecode'), $value);
        } else if (is_scalar($value)) {
            return rawurldecode($value);
        } else {
            return '';
        }
    }
}
