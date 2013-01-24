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

/**
 * OAuth token.
 *
 * @author Rodolfo Puig <rpuig@7gstudios.com>
 */
class OAuthToken {
    /**
     * Token.
     *
     * @var string
     */
    protected $token;

    /**
     * Token secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Constructor.
     *
     * @param string $token
     * @param string $secret
     */
    public function __construct($token, $secret = NULL) {
        $this->token  = $token;
        $this->secret = $secret;
    }

    /**
     * Returns the token.
     *
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Returns the token secret.
     *
     * @return string
     */
    public function getSecret() {
        return $this->secret;
    }

    /**
     * Returns the string representation of this object.
     *
     * @return string
     */
    function __toString() {
        return "OAuthToken[token=" . $this->token . ",secret=" . $this->secret . "]";
    }
}
