<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Assigner;

use Rednose\FrameworkBundle\Model\GroupInterface;
use Rednose\FrameworkBundle\Model\GroupManagerInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GroupAssigner implements AssignerInterface
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
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param GroupManagerInterface $manager
     * @param ExpressionLanguage    $language
     */
    public function __construct(GroupManagerInterface $manager, SessionInterface $session, $language = null)
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
    }

    /**
     * @param string $username
     *
     * @return GroupInterface[]
     */
    public function resolve($username)
    {
        return [];
    }
}