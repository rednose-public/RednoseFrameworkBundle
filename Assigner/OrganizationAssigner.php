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
                if ($this->language->evaluate($condition, $this->context->get($username))) {
                    return $conditions['priority'][$offset];
                }
            } catch (\Exception $e) {}
        }

        return false;
    }

    /**
     * @param array $conditions
     *
     * @return array
     */
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
}