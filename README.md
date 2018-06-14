RednoseFrameworkBundle [![Build status...](https://secure.travis-ci.org/rednose-public/RednoseFrameworkBundle.png?branch=master)](http://travis-ci.org/rednose-public/RednoseFrameworkBundle)
======================

Provides several core features used throughout the RedNose bundles.

**Caution:** This bundle Requires Symfony 2.8.x or higher.

Installation
------------
Add FrameworkBundle to your application kernel:

```php
// app/AppKernel.php

public function registerBundles()
{
    return [
        // ...
        new Rednose\FrameworkBundle\RednoseFrameworkBundle(),
        // ...
    ];
}
```

Configuration
-------------
Configuration reference:

```yml
# app/config/config.yml

rednose_framework:
    user: true # Enables user / group account support

    auto_account_creation: false # Auto creates user accounts on first login, useful for single-sign-on

    acl: false # Enables ACL services and permission maps
```

* [Enable SAML support](Resources/doc/enabling_saml_support.md)

About
-----

FrameworkBundle is a [RedNose](https://www.rednose.nl) initiative.

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

[LICENSE](LICENSE)
