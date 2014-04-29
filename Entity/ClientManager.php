<?php

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Entity\ClientManager as BaseClientManager;

class ClientManager extends BaseClientManager
{
    public function findClientsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    public function findClientByPublicId($publicId)
    {
        return $this->findClientBy(array(
            'randomId'  => $publicId,
        ));
    }
}
