<?php

namespace Rednose\FrameworkBundle\Security\Authorization\Voter;

use Rednose\FrameworkBundle\Entity\HasOrganizationInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class HasOrganizationVoter implements VoterInterface
{
    const VIEW = 'view';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [self::VIEW]);
    }

    public function supportsClass($class)
    {
        return is_subclass_of($class, 'Rednose\FrameworkBundle\Entity\HasOrganizationInterface');
    }

    /**
     * @var HasOrganizationInterface $object
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $attribute = $attributes[0];

        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($object->getOrganization() === $user->getOrganization()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}