Socialite - Social interation for PHP 5.3+
==========================================

Usage
-----

    use Socialite\Twitter\Api\RestClient;
    use Socialite\Twitter\Api\StreamClient;

    // create a Twitter REST API client
    $rest = new RestClient();

    // create a Twitter Stream API client
    $stream = new StreamClient();

Core Concepts
-------------

Coming soon...

About
=====

Requirements
------------

- Any flavor of PHP 5.3 or greater should do

Submitting bugs and feature requests
------------------------------------

Bugs and feature request are tracked on [GitHub](https://github.com/telemundo/socialite/issues)

Author
------

Rodolfo Puig - <rodolfo.puig@nbcuni.com> - <https://twitter.com/rpuig_nbcuni><br />
See also the list of [contributors](https://github.com/telemundo/socialite/contributors) which participated in this project.

License
-------

Socialite is licensed under the MIT License - see the LICENSE file for details

Acknowledgements
----------------

This library is heavily inspired by the [Phirehose](https://github.com/fennb/phirehose) and [twitteroauth](https://github.com/abraham/twitteroauth) libraries, although most concepts have been adjusted to fit to the PHP 5.3+ namespace paradigm.
