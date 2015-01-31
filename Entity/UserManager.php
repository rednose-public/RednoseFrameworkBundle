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

use Rednose\FrameworkBundle\Model\UserInterface;
use Rednose\FrameworkBundle\Model\UserManagerInterface;

use AerialShip\SamlSPBundle\Bridge\SamlSpInfo;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AerialShip\SamlSPBundle\Security\Core\User\UserManagerInterface as SamlUserManagerInterface;

class UserManager extends BaseUserManager implements UserManagerInterface, SamlUserManagerInterface
{
    /**
     * @var bool
     */
    protected $autoAccountCreation;

    /**
     * @var string|null
     */
    protected $samlUsernameAttr = null;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     * @param ObjectManager           $om
     * @param string                  $class
     * @param bool                    $autoAccountCreation
     * @param string|null             $samlUserAttr
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, ObjectManager $om, $class, $autoAccountCreation = false, $samlUserAttr = null)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $om, $class);

        $this->autoAccountCreation = $autoAccountCreation;
        $this->samlUsernameAttr    = $samlUserAttr;
    }

    /**
     * @return User
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
     * Attempt to authenticate using a daily logic token.
     * This method looks in the request object for the following
     * post or get variables: 'token' and 'username'
     *
     * The token is a sha1 of the sha1 username of the supplied username and
     * a sha1 of the date (notation: 01-01-1970) followed by
     * the secret in the parameters(.ini|.yml|.xml).
     * Example: sha1(sha1('username') . sha1(date('d-m-Y') . 'token'));
     *
     * Warning: The application secret is used as a private key between application
     * and must therefor remain secret!
     *
     * @param object $container
     *
     * @return boolean
     *
     * @throws \InvalidArgumentException when the secret parameter is invalid.
     * @throws AccessDeniedException when the token is invalid.
     */
    public function tokenAuthentication($container)
    {
        $request = $container->get('request');
        $secret = trim($container->getParameter('kernel.secret'));

        if ($request->get('token') && $request->get('username')) {
            if ($secret === '' || $secret === 'ThisTokenIsNotSoSecretChangeIt') {
                throw new \InvalidArgumentException('Secret parameter invalid');
            }

            $token = sha1(
                sha1($request->get('username')) .
                sha1(date('d-m-Y') . $secret)
            );

            if ($token !== trim($request->get('token'))) {
                throw new AccessDeniedException('Token not accepted');
            }

            $container->get('fos_user.security.login_manager')->loginUser(
                $container->getParameter('fos_user.firewall_name'),
                $this->loadUserByUsername($request->get('username'))
            );

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function createUserFromSamlInfo(SamlSpInfo $samlInfo)
    {
        /* See loadUserByUsername */
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserBySamlInfo(SamlSpInfo $samlInfo)
    {
        if ($this->samlUsernameAttr !== null) {
            $attrs = $samlInfo->getAttributes();

            foreach ($attrs as $attr) {
                if ($attr->getName() === $this->samlUsernameAttr) {
                    $username = $attr->getFirstValue();

                    break;
                }
            }
        } else {
            $username = $samlInfo->getNameID()->getValue();
        }

        return $this->loadUserByUsername($username);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        if ($this->autoAccountCreation && $this->findUserBy(array('username' => $username)) === null) {
            $user = $this->createUser();
            $user->setUserName($username);
            $user->setEnabled(true);
            $user->setEmail($username);
            $user->setPassword($this->randomPassword());
            $this->updateUser($user);
        }

        return parent::loadUserByUsername($username);
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
