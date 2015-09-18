<?php

namespace Rednose\FrameworkBundle\Exception;

use Exception;
use Rednose\FrameworkBundle\Model\OrganizationInterface;

class OrganizationIntegrityException extends \Exception
{
    /**
     * @param OrganizationInterface $expected
     * @param OrganizationInterface $actual
     */
    public function __construct(OrganizationInterface $expected, OrganizationInterface $actual)
    {
        parent::__construct(
            sprintf('Invalid organization context \'%s\', expected organization \'%s\'', $actual->getName(), $expected->getName())
        );
    }
}