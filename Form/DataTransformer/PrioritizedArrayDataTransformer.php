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

use Rednose\FrameworkBundle\Model\PrioritizedArray;
use Symfony\Component\Form\DataTransformerInterface;
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

    public function __construct(Request $request, $formName)
    {
        $this->request  = $request;
        $this->formName = $formName;
    }

    public function transform($data)
    {
        if ($this->request->getMethod() === 'POST' && $data !== null) {
            // Clear the form data only when it is actually posted.
            // Otherwise we may accidentally remove all items.

            $getKeys = function ($ar, &$keys) use (&$getKeys) {
                foreach ($ar as $k => $v) {
                    $keys[$k] = true;

                    if (is_array($v) === true) {
                        $getKeys($v, $keys);
                    }
                }
            };

            $postVariables = $this->request->request->all();

            $postKeys = [];
            $getKeys($postVariables, $postKeys);

            if (isset($postKeys[$this->formName]) && $postKeys[$this->formName] === true) {
                $data->clear();
            }
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
