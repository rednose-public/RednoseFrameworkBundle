<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;

/**
 * Custom permission map that adds the 'MOVE', 'UPLOAD' and 'APPROVE' permissions.
 */
class PermissionMap implements PermissionMapInterface
{
    const PERMISSION_VIEW     = 'VIEW';
    const PERMISSION_EDIT     = 'EDIT';
    const PERMISSION_CREATE   = 'CREATE';
    const PERMISSION_DELETE   = 'DELETE';
    const PERMISSION_UNDELETE = 'UNDELETE';
    const PERMISSION_OPERATOR = 'OPERATOR';
    const PERMISSION_MASTER   = 'MASTER';
    const PERMISSION_OWNER    = 'OWNER';
    const PERMISSION_MOVE     = 'MOVE';
    const PERMISSION_UPLOAD   = 'UPLOAD';
    const PERMISSION_APPROVE  = 'APPROVE';

    private $map = array(
        self::PERMISSION_VIEW => array(
            MaskBuilder::MASK_VIEW,
            MaskBuilder::MASK_EDIT,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_EDIT => array(
            MaskBuilder::MASK_EDIT,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_CREATE => array(
            MaskBuilder::MASK_CREATE,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_DELETE => array(
            MaskBuilder::MASK_DELETE,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_UNDELETE => array(
            MaskBuilder::MASK_UNDELETE,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_OPERATOR => array(
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_MASTER => array(
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_OWNER => array(
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_MOVE => array(
            MaskBuilder::MASK_MOVE,
        ),

        self::PERMISSION_UPLOAD => array(
            MaskBuilder::MASK_UPLOAD,
        ),

        self::PERMISSION_APPROVE => array(
            MaskBuilder::MASK_APPROVE,
        ),
    );

    /**
     * {@inheritDoc}
     */
    public function getMasks($permission, $object)
    {
        if (!isset($this->map[$permission])) {
            return null;
        }

        return $this->map[$permission];
    }

    /**
     * {@inheritDoc}
     */
    public function contains($permission)
    {
        return isset($this->map[$permission]);
    }
}
