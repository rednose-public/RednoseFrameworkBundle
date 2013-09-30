<?php

namespace Rednose\FrameworkBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use OAuth2\OAuth2;

/**
 * OAuth client.
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_client")
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPublicId()
    {
        return $this->getRandomId();
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
