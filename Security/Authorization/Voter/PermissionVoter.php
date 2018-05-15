<?php

namespace Rednose\FrameworkBundle\Security\Authorization\Voter;

use Rednose\FrameworkBundle\Entity\HasOrganizationInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PermissionVoter implements VoterInterface
{
    const PREFIX_ = 'PERMISSION_';

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function supportsAttribute($attribute)
    {
        return (strpos($attribute, self::PREFIX_) !== false);
    }

    public function supportsClass($class)
    {
        return false;
    }

    /**
     * @var HasOrganizationInterface $object
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {

        $attribute = $attributes[0];

        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!$this->tokenStorage->getToken()) {
            return VoterInterface::ACCESS_DENIED;
        }

        $user        = $this->tokenStorage->getToken()->getUser();
        $permissions = $user->getPermissions();

        if (array_search($attribute, $permissions, true)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}