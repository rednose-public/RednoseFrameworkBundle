<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Assigner;

use Rednose\FrameworkBundle\Context\SessionContext;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\OrganizationManagerInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class OrganizationAssigner extends AbstractAssigner implements AssignerInterface
{
    /**
     * @var OrganizationManagerInterface
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
     * @param OrganizationManagerInterface $manager
     * @param SessionContext               $context
     * @param ExpressionLanguage|null      $language
     */
    public function __construct(OrganizationManagerInterface $manager, SessionContext $context, ExpressionLanguage $language = null)
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
        $organization = $this->resolve($user->getUsername());

        if ($organization) {
            $user->setOrganization($organization);
        }
    }

    /**
     * @param string $username
     *
     * @return OrganizationInterface
     */
    public function resolve($username)
    {
        $organizations = [];
        $conditions    = [];

        foreach ($this->manager->findOrganizations() as $organization) {
            $conditions[$organization->getId()]    = $organization->getConditions();
            $organizations[$organization->getId()] = $organization;
        }

        $match = $this->shouldAssignPrioritized(
            $username, $conditions, $this->context, $this->language
        );

        if ($match) {
            return $organizations[$match['source']];
        }

        return null;
    }
}