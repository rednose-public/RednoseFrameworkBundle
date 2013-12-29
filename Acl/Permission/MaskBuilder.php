<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\MaskBuilder as BaseMaskBuilder;

/**
 * Maskbuilder for the custom permission map.
 */
class MaskBuilder extends BaseMaskBuilder
{
    const MASK_VIEW     = 1;          // 1 << 0
    const MASK_CREATE   = 2;          // 1 << 1
    const MASK_EDIT     = 4;          // 1 << 2
    const MASK_DELETE   = 8;          // 1 << 3
    const MASK_UNDELETE = 16;         // 1 << 4
    const MASK_OPERATOR = 32;         // 1 << 5
    const MASK_MASTER   = 64;         // 1 << 6
    const MASK_OWNER    = 128;        // 1 << 7
    const MASK_MOVE     = 256;        // 1 << 8
    const MASK_UPLOAD   = 512;        // 1 << 9
    const MASK_APPROVE  = 1024;        // 1 << 9
    const MASK_IDDQD    = 1073741823; // 1 << 0 | 1 << 1 | ... | 1 << 30
    const CODE_VIEW     = 'V';
    const CODE_CREATE   = 'C';
    const CODE_EDIT     = 'E';
    const CODE_DELETE   = 'D';
    const CODE_UNDELETE = 'U';
    const CODE_OPERATOR = 'O';
    const CODE_MASTER   = 'M';
    const CODE_OWNER    = 'N';
    const CODE_MOVE     = 'H';
    const CODE_UPLOAD   = 'L';
    const CODE_APPROVE  = 'A';
    const ALL_OFF       = '................................';
    const OFF           = '.';
    const ON            = '*';
}
