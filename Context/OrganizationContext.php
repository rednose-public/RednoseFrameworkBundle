<?php

namespace Rednose\FrameworkBundle\Context;

use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrganizationContext
{
    /**
     * @var TokenStorageInterface
     */
    protected $context;

    /**
     * @param TokenStorageInterface $context
     */
    public function __construct(TokenStorageInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        if (!$this->context->getToken()) {
            return null;
        }

        $user = $this->context->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return null;
        }

       return $user->getOrganization();
    }
}