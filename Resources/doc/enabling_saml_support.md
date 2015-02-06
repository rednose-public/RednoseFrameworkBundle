Enabling SAML support
===================

Before applying this configuration make sure you are already using the user management
mechanisms provided by the RedNoseFrameworkBundle.

Step 1: Add SamlSpBundle dependency to AppKernel.php
----------------------------------------------------
- Add Bundle class
<pre>
...
    new AerialShip\SamlSPBundle\AerialShipSamlSPBundle(),
...
</pre>

- Add SAML routing
<pre>
# SAML
aerialship_saml_sp_bundle:
    resource: "@AerialShipSamlSPBundle/Resources/config/routing.yml"
</pre>

- Add SAML entity mapping to config.yml
<pre>
doctrine:
    orm:
        mappings:
            saml:
                type:      annotation
                dir:       %kernel.root_dir%/../vendor/rednose/framework-bundle/Rednose/FrameworkBundle/Saml
                prefix:    Rednose\FrameworkBundle\Saml\SsoState
                alias:     SsoState
                is_bundle: false
</pre>

Step 2: FederationMetadata exchange
-----------------------------------

- Request the federated metadata of the idP from your supplier and place it somewhere in your project.

- Goto /saml/sp/FederationMetadata.xml to download the generated metadata of your project (the SP metadata) and provide it to your idP supplier.
> Note: check the entityId in the xml it should match the one you configure in your firewall. If your firewall is already configured it is automaticly set the the correct value.


Step 3: Configure firewall
--------------------------

Edit securiy.yml to define the pattern that the SAML security should be applied to.

- Edit the file property to point to the federated xml file you added to your project.

For example:
<pre>
    firewalls:
        saml:
            pattern: ^/
            anonymous: true
            aerial_ship_saml_sp:
                local_logout_path: /logout
                provider: rednose_frameworkbundle
                services:
                    identityProvider:
                        idp:
                            file: "@AcmeYourBunde/Resources/config/idp-FederationMetadata.xml"
                        sp:
                            config:
                                entity_id: yourEntityId
            logout:
                path: /logout

</pre>

Step 4: Configure rednose_framework
-----------------------------------

By default the usermanager will try to read the username from the NameID object, however you can also read
the username from a attribute provided by the idP. If you wish to do this add the following to your rednose_framework config.

<pre>
rednose_framework:
    saml:
        username_attr: someAttrName
</pre>

Auto account creation is disabled by default, if you wish to enable this feature add the following:
<pre>
rednose_framework:
    auto_account_creation: true
</pre>
