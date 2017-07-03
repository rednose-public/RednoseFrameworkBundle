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

use Rednose\FrameworkBundle\Entity\File;
use Rednose\FrameworkBundle\Model\FileInterface;
use Rednose\FrameworkBundle\Model\PrioritizedArray;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PrioritizedArrayDataTransformer implements DataTransformerInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $formName;

    public function __construct($formName, Request $request)
    {
        $this->request = $request;
        $this->formName = $formName;
    }

    public function transform($data)
    {
        if ($this->request->getMethod() === 'POST' && $data !== null) {
            $data->clear();
        }

        return $data;
    }

    public function reverseTransform($data)
    {
        if ($data instanceof PrioritizedArray) {
            return $data;
        }

        return null;
    }
}
