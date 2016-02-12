<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Assigner;

use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\OrganizationManagerInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class OrganizationAssigner
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
     * @param OrganizationManagerInterface $manager
     * @param ExpressionLanguage           $language
     */
    public function __construct(OrganizationManagerInterface $manager, $language = null)
    {
        $this->language = $language ?: new ExpressionLanguage();
        $this->manager = $manager;
    }

    /**
     * @param UserInterface $user
     */
    public function assign(UserInterface $user)
    {
        if ($user->isStatic()) {
            return;
        }

        $organization = $this->resolve($user->getUsername(false));

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
        foreach ($this->manager->findOrganizations() as $organization) {
            if ($this->shouldAssign($organization, $username)) {
                return $organization;
            }
        }

        return null;
    }

    /**
     * @param OrganizationInterface $organization
     * @param string                $username
     *
     * @return bool
     */
    protected function shouldAssign(OrganizationInterface $organization, $username)
    {
        foreach ($organization->getConditions() as $condition) {
            try {
                if ($this->language->evaluate($condition, $this->createContext($username))) {
                    return true;
                }
            } catch (\Exception $e) {}
        }

        return false;
    }

    /**
     * @param string $username
     *
     * @return array
     */
    protected function createContext($username)
    {
        return ['user' => (object) [
            'username' => $username
        ]];
    }
}