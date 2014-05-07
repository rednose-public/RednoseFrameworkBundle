<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Acl;

use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

class AclManipulator
{
    /**
     * @var MutableAclProviderInterface
     */
    protected $aclProvider;

    /**
     * Constructor.
     *
     * @param MutableAclProviderInterface $aclProvider
     */
    public function __construct(MutableAclProviderInterface $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    /**
     * @param $object
     *
     * @return AclInterface
     */
    public function getAcl($object)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);

        try {
            return $this->aclProvider->findAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            return $this->aclProvider->createAcl($objectIdentity);
        }
    }

    /**
     * @param MutableAclInterface  $acl
     * @param UserSecurityIdentity $securityIdentity
     * @param integer              $mask
     */
    public function updateAcl(MutableAclInterface $acl, $securityIdentity, $mask)
    {
        $index = null;
        $ace   = null;

        foreach ($acl->getObjectAces() as $currentIndex => $currentAce) {
            /** @var EntryInterface $currentAce */
            if ($currentAce->getSecurityIdentity()->equals($securityIdentity)) {
                $index = $currentIndex;
                $ace   = $currentAce;

                break;
            }
        }

        if ($ace) {
            $acl->updateObjectAce($index, $mask);
        } else {
            $acl->insertObjectAce($securityIdentity, $mask);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * @param AclInterface         $acl
     * @param UserSecurityIdentity $securityIdentity
     * @param integer              $mask
     *
     * @return boolean
     */
    public function isGrantedMask(AclInterface $acl, UserSecurityIdentity $securityIdentity, $mask)
    {
        try {
            return $acl->isGranted(array($mask), array($securityIdentity));
        } catch (NoAceFoundException $e) {
            return false;
        }
    }
} 