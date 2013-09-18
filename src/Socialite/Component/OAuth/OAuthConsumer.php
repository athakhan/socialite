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
 * OAuth consumer.
 *
 * @author Rodolfo Puig <rodolfo@puig.io>
 */
class OAuthConsumer {
    /**
     * Consumer key.
     *
     * @var string
     */
    public $key;

    /**
     * Consumer secret.
     *
     * @var string
     */
    public $secret;

    /**
     * Constructor.
     *
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret) {
        $this->key    = $key;
        $this->secret = $secret;
    }

    /**
     * Returns the consumer key.
     *
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Returns the consumer secret.
     *
     * @return string
     */
    public function getSecret() {
        return $this->secret;
    }

    /**
     * Return the string representation of this object.
     *
     * @return string
     */
    function __toString() {
        return "OAuthConsumer[key=" . $this->key . ",secret=" . $this->secret . "]";
    }
}
