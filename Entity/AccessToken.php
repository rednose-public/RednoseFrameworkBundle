<?php

namespace Rednose\FrameworkBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use FOS\OAuthServerBundle\Model\ClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * OAuth access token.
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_access_token")
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Rednose\FrameworkBundle\Entity\User")
     */
    protected $user;

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
}
