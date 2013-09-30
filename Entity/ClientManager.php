<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Entity\ClientManager as BaseClientManager;

class ClientManager extends BaseClientManager
{
    public function findClientByPublicId($publicId)
    {
        return $this->findClientBy(array(
            'randomId'  => $publicId,
        ));
    }
}