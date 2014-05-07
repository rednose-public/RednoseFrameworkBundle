<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Acl\Domain;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\SecurityIdentityRetrievalStrategy;
use Rednose\FrameworkBundle\Model\UserInterface;
use Rednose\FrameworkBundle\Model\GroupInterface;

/**
 * Extends the base SecurityIdentityRetrievalStrategy to add the notion of UserGroups
 * to the ACL system.
 */
class GroupSecurityIdentityRetrievalStrategy extends SecurityIdentityRetrievalStrategy
{
    /**
     * {@inheritDoc}
     */
    public function getSecurityIdentities(TokenInterface $token)
    {
        // The parent class adds the User security identity and the roles.
        $sids = parent::getSecurityIdentities($token);

        $user = $token->getUser();

        if ($user instanceof UserInterface) {
            $groups = $user->getGroups();

            foreach ($groups as $group) {
                // Add all groups to the list of security id's.
                if ($group instanceof GroupInterface) {
                    $sids[] = new UserSecurityIdentity($group->getName(), get_class($group));
                }
            }
        }

        return $sids;
    }
}
