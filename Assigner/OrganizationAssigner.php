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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrganizationAssigner implements AssignerInterface
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
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param OrganizationManagerInterface $manager
     * @param ExpressionLanguage           $language
     */
    public function __construct(OrganizationManagerInterface $manager, SessionInterface $session, $language = null)
    {
        $this->language = $language ?: new ExpressionLanguage();
        $this->session  = $session;
        $this->manager  = $manager;
    }

    /**
     * @param UserInterface $user
     */
    public function assign(UserInterface $user)
    {
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
        $organizations = [];

        foreach ($this->manager->findOrganizations() as $organization) {
            $priority = $this->shouldAssign($organization, $username);

            if ($priority !== false) {
                $priority = (int)$priority;

                $organizations[$priority] = $organization;
            }
        }

        if (count($organizations) > 0) {
            krsort($organizations, SORT_NUMERIC);

            return array_shift($organizations);
        }

        return null;
    }

    /**
     * @param OrganizationInterface $organization
     * @param string                $username
     *
     * @return bool|int
     */
    protected function shouldAssign(OrganizationInterface $organization, $username)
    {
        $conditions = $this->normalizeConditions($organization->getConditions());

        if (count($conditions) === 0) {
            return false;
        }

        foreach ($conditions['conditions'] as $offset => $condition) {
            try {
                if ($this->language->evaluate($condition, $this->createContext($username))) {
                    return $conditions['priority'][$offset];
                }
            } catch (\Exception $e) {}
        }

        return false;
    }

    protected function normalizeConditions($conditions)
    {
        if (count($conditions) !== 2) {
            return [];
        }

        $first  = array_pop($conditions);
        $second = array_pop($conditions);

        if (is_array($first)) {
            if (isset($first[0])) {
                $key = $first[0];

                if (strpos($key, 'priority') !== false) {
                    return ['conditions' => $second[1], 'priority' => $first[1]];
                }

                return ['conditions' => $first[1], 'priority' => $second[1]];
            }
        }

        return [];
    }

    /**
     * @param string $username
     *
     * @return array
     */
    protected function createContext($username)
    {
        $session = [];

        foreach ($this->session->all() as $key => $sessionItem) {
            if (is_object($sessionItem)) {
                $session[$key] = $sessionItem;
            }
        }

        return [
            'user' => (object) [
                'username' => $username
            ],
            'session' => (object) $session
        ];
    }
}