<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Acl;

use Rednose\FrameworkBundle\Acl\Model\ObjectAclData;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

class ObjectAclManipulator
{
    /**
     * @var AclManipulator
     */
    protected $aclManipulator;

    /**
     * @var string
     */
    protected $maskBuilderClass;

    /**
     * @var array
     */
    protected $permissions;

    /**
     * @var array
     */
    protected $masks;

    /**
     * Constructor.
     *
     * @param AclManipulator $aclManipulator
     * @param string         $maskBuilderClass
     * @param array          $permissions
     */
    public function __construct(AclManipulator $aclManipulator, $maskBuilderClass, array $permissions = array('CREATE', 'EDIT', 'APPROVE'))
    {
        $this->aclManipulator   = $aclManipulator;
        $this->maskBuilderClass = $maskBuilderClass;
        $this->permissions      = $permissions;

        // Initialize masks.
        $this->masks = array();

        $reflectionClass = new \ReflectionClass(new $maskBuilderClass());

        foreach ($permissions as $permission) {
            $this->masks[$permission] = $reflectionClass->getConstant('MASK_' . $permission);
        }
    }

    /**
     * @param object $object
     * @param array  $identities
     *
     * @return ObjectAclData
     */
    public function createObjectAclData($object, array $identities)
    {
        $acl = $this->aclManipulator->getAcl($object);

        $data = array();

        foreach ($identities as $identity) {
            $securityIdentity = new UserSecurityIdentity($identity, get_class($identity));

            $entry = array();

            foreach ($this->permissions as $permission) {
                $entry[$permission] = $this->aclManipulator->isGrantedMask($acl, $securityIdentity, $this->masks[$permission]);
            }

            $data[$identity->getId()] = $entry;
        }

        $objectAclData = new ObjectAclData($object, $identities, $this->permissions);
        $objectAclData->setData($data);

        return $objectAclData;
    }

    /**
     * @param ObjectAclData $objectAclData
     */
    public function updateObjectAclData(ObjectAclData $objectAclData)
    {
        $data = $objectAclData->getData();

        foreach ($objectAclData->getIdentities() as $identity) {
            $securityIdentity = new UserSecurityIdentity($identity, get_class($identity));

            /** @var \Rednose\FrameworkBundle\Acl\Permission\MaskBuilder $maskBuilder */
            $maskBuilder = new $this->maskBuilderClass();

            foreach ($objectAclData->getPermissions() as $permission) {
                if ($data[$identity->getId()][$permission]) {
                    $maskBuilder->add($permission);
                }
            }

            $mask = $maskBuilder->get();
            $acl  = $this->aclManipulator->getAcl($objectAclData->getObject());

            $this->aclManipulator->updateAcl($acl, $securityIdentity, $mask);
        }
    }
}
