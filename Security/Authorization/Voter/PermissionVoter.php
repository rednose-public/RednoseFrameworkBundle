<?php

namespace Rednose\FrameworkBundle\Security\Authorization\Voter;

use Rednose\FrameworkBundle\Entity\HasOrganizationInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
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

    public function supportsAttribute($attribute)
    {
        return (strpos($attribute, self::PREFIX_) !== false);
    }

    public function supportsClass($class)
    {
        return false;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {

        $attribute = $attributes[0];

        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        if (($user instanceof UserInterface) === false) {
            return VoterInterface::ACCESS_DENIED;
        }

        $permissions = $user->getPermissions();

        if (array_search($attribute, $permissions, true) !== false) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}