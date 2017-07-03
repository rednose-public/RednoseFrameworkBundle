<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Assigner;

use Rednose\FrameworkBundle\Context\SessionContext;
use Rednose\FrameworkBundle\Model\GroupInterface;
use Rednose\FrameworkBundle\Model\GroupManagerInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class GroupAssigner extends AbstractAssigner implements AssignerInterface
{
    /**
     * @var GroupManagerInterface
     */
    protected $manager;

    /**
     * @var ExpressionLanguage
     */
    protected $language;

    /**
     * @var SessionContext
     */
    protected $context;

    /**
     * @param GroupManagerInterface   $manager
     * @param SessionContext          $context
     * @param ExpressionLanguage|null $language
     */
    public function __construct(GroupManagerInterface $manager, SessionContext $context, ExpressionLanguage $language = null)
    {
        $this->language = $language ?: new ExpressionLanguage();
        $this->context  = $context;
        $this->manager  = $manager;
    }

    /**
     * @param UserInterface $user
     */
    public function assign(UserInterface $user)
    {
        foreach ($user->getGroups() as $group) {
            $user->removeGroup($group);
        }

        /** @var GroupInterface $group */
        foreach ($this->manager->findGroupsByOrganization($user->getOrganization()) as $group) {
            $match = $this->shouldAssignPrioritized(
                $user->getUsername(),
                [ $group->getId() => $group->getConditions() ],
                $this->context,
                $this->language
            );

            if ($match) {
                $user->addGroup($group);
            }
        }
    }
}