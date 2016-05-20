<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Rednose\FrameworkBundle\Event\UserEvent;
use Rednose\FrameworkBundle\Events;
use Rednose\FrameworkBundle\Model\UserInterface;
use Rednose\FrameworkBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager extends BaseUserManager implements UserManagerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var bool
     */
    protected $autoAccountCreation;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface  $encoderFactory
     * @param CanonicalizerInterface   $usernameCanonicalizer
     * @param CanonicalizerInterface   $emailCanonicalizer
     * @param ObjectManager            $om
     * @param string                   $class
     * @param EventDispatcherInterface $dispatcher
     * @param bool                     $autoAccountCreation
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, ObjectManager $om, $class, EventDispatcherInterface $dispatcher, $autoAccountCreation = false)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $om, $class);

        $this->dispatcher = $dispatcher;
        $this->autoAccountCreation = $autoAccountCreation;
    }

    /**
     * @return UserInterface
     */
    public function createUser()
    {
        return parent::createUser();
    }

    /**
     * Return all users, sorted
     *
     * @param bool $ascending
     *
     * @return UserInterface[]
     */
    public function findUsersSorted($ascending = true)
    {
        if ($ascending) {
            $direction = 'asc';
        } else {
            $direction = 'desc';
        }

        return $this->repository->findBy(array(), array('username' => $direction));
    }

    /**
     * @param string $username
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        if ($this->autoAccountCreation && !$this->findUserBy(array('username' => $username))) {
            $user = $this->createUser();

            $user->setUsername($username);
            $user->setEnabled(true);
            $user->setEmail($username);
            $user->setPassword($this->randomPassword());
//            $user->setStatic(false);

            $event = new UserEvent($user);
            $this->dispatcher->dispatch(Events::USER_AUTO_CREATE, $event);

            $this->updateUser($user);
        }

        $user = parent::loadUserByUsername($username);

        $this->dispatcher->dispatch(Events::USER_LOGIN, new UserEvent($user));

        return $user;
    }

    /**
     * @param int $length
     *
     * @return string
     */
    protected function randomPassword($length = 9)
    {
        $vowels = 'aeuy';

        $consonants = 'bdghjmnpqrstvz';
        $consonants .= '@#$%';

        $password = '';
        $alt = time() % 2;

        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }

        return $password;
    }
}
