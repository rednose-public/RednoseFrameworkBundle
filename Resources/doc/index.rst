Provides several core features used throughout the RedNose bundles.

Installation
============

Add FrameworkBundle to your application kernel
---------------------------------------------

::

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Rednose\FrameworkBundle\RednoseFrameworkBundle(),
            // ...
        );
    }

Security configuration
----------------------

::

    # app/config/config.yml
    rednose_framework:
        oauth: true (Enables Oauth2 authentication)
