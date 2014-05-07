<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Model\Node\Value;

use Rednose\FrameworkBundle\Model\Node\OutputNodeInterface;

/**
 * Output node that receives a value from an input node.
 */
interface OutputValueNodeInterface extends OutputNodeInterface
{
    /**
     * @param string $input
     */
    public function setInputValue($input);
}
