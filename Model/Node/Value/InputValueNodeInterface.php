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

use Rednose\FrameworkBundle\Model\Node\InputNodeInterface;

/**
 * Input node that supplies a value to an output node.
 */
interface InputValueNodeInterface extends InputNodeInterface
{
    /**
     * @return string
     */
    public function getOutputValue();
}
