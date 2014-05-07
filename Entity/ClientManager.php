<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
