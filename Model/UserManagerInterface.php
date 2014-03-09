<?php

namespace Rednose\FrameworkBundle\Model;

use FOS\UserBundle\Model\UserManagerInterface as BaseUserManagerInterface;
use Rednose\FrameworkBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface UserManagerInterface extends BaseUserManagerInterface
{
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
     * @param ContainerInterface $container
     *
     * @return boolean
     */
    public function tokenAuthentication($container);

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username);

    /**
     * Return all users, sorted
     *
     * @param $ascending
     */
    public function findUsersSorted($ascending = true);
}
