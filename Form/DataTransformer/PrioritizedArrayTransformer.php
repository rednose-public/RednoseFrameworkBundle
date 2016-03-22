<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PrioritizedArrayTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        //die(print_R($value));
        return $value;
    }

    public function reverseTransform($value)
    {
        die(print_R($value));
    }
}
