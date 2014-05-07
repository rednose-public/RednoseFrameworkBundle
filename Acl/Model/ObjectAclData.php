<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Acl\Model;

class ObjectAclData
{
    /**
     * @var object
     */
    protected $object;

    /**
     * @var array
     */
    protected $identities;

    /**
     * @var array
     */
    protected $permissions;

    /**
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param object $object
     * @param array  $identities
     * @param array  $permissions
     */
    public function __construct($object, array $identities, array $permissions)
    {
        $this->object      = $object;
        $this->identities  = $identities;
        $this->permissions = $permissions;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return object[]
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    /**
     * @return string[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data ? $this->data : array();
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
