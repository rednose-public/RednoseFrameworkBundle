<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class ObjectAclDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['data'])) {
            throw new \InvalidArgumentException('Data needs to be passed on form type construction');
        }

        // TODO: Implement datatransformer
        foreach ($options['data'] as $identity => $entry) {
            foreach ($entry as $permission => $value) {
                $builder->add($identity.$permission, 'checkbox', array('required' => false, 'data' => $value));
            }
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => null);
    }

    public function getName()
    {
        return 'rednose_framework_acl';
    }
}
