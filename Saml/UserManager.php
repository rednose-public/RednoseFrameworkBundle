<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Saml;

use AerialShip\SamlSPBundle\Bridge\SamlSpInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Rednose\FrameworkBundle\Entity\UserManager as BaseUserManager;
use AerialShip\SamlSPBundle\Security\Core\User\UserManagerInterface as SamlUserManagerInterface;

class UserManager extends BaseUserManager implements SamlUserManagerInterface
{
    /**
     * @var string|null
     */
    protected $samlUsernameAttr = null;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface  $encoderFactory
     * @param CanonicalizerInterface   $usernameCanonicalizer
     * @param CanonicalizerInterface   $emailCanonicalizer
     * @param EntityManagerInterface   $om
     * @param string                   $class
     * @param EventDispatcherInterface $dispatcher
     * @param bool                     $autoAccountCreation
     * @param string|null              $samlUserAttr
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, EntityManagerInterface $om, $class, EventDispatcherInterface $dispatcher, $autoAccountCreation = false, $samlUserAttr = null)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $om, $class, $dispatcher, $autoAccountCreation);

        $this->samlUsernameAttr = $samlUserAttr;
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
        if ($this->samlUsernameAttr) {
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
}
