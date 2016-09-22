RednoseFrameworkBundle [![Build status...](https://secure.travis-ci.org/rednose-public/RednoseFrameworkBundle.png?branch=master)](http://travis-ci.org/rednose-public/RednoseFrameworkBundle)
======================

Provides several core features used throughout the RedNose bundles.

**Caution:** This bundle Requires Symfony 2.3.x or higher.

Installation
------------
Add FrameworkBundle to your application kernel:

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new Rednose\FrameworkBundle\RednoseFrameworkBundle(),
            // ...
        );
    }

Configuration
-------------
Configuration reference:

    # app/config/config.yml

    rednose_framework:
    	user: true # Enables user / group account support

		auto_account_creation: false # Auto creates user accounts on first login, useful for single-sign-on

        acl:   false # Enables ACL services and permission maps

* [Enable SAML support](Resources/doc/Enable SAML support.md)

About
-----

FrameworkBundle is a [RedNose](http://www.rednose.nl) initiative.

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE
