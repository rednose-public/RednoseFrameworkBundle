<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\DataEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PrioritizedArrayDataListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => array('onPreSubmit', 999)
        );
    }

    public function preSetData(FormEvent $event)
    {
        $form     = $event->getForm();
        $formName = $form->getName();
        $data     = $event->getData();

        if (null === $data) {
            $data = array();
        }

        $data = $this->normalize($data);

        if (is_array($data) && count($data) === 2) {
            foreach ($data[0] as $name => $value) {
                $form->add($formName . '_' . $name, 'text', [ 'data' => $value ]);

                $form->add('priority_' . $name, 'choice', [ 'data' => $data[1][$name], 'choices' => [
                    0 => 'Normal', 1 => 'High', 2 => 'Very High'
                ]]);
            }
        }

        $event->setData([]);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form   = $event->getForm();
        $data   = $event->getData();

        if (null === $data) {
            $data = array();
        }

        // 0 = Data
        // 1 = Priority

        foreach ($form as $child) {
            $form->remove($child->getName());
        }

        $buffer = ['priority' => [], $form->getName() => []];

        foreach ($data as $key => $data) {
            if (strpos($key, 'priority') !== false) {
                $buffer['priority'][] = $data;
            } else {
                $buffer[$form->getName()][] = $data;
            }
        }

        foreach ($buffer as $type => $arr) {
            $form->add($type, 'collection', [ 'empty_data' => [ $type, $arr ] ]);
        }

        $event->setData([]);
    }

    protected function normalize($data)
    {
        $first  = array_pop($data);
        $second = array_pop($data);

        if (is_array($first)) {
            if (isset($first[0])) {
                $key = $first[0];

                if (strpos($key, 'priority') !== false) {
                    return [$second[1], $first[1]];
                }

                return [$first[1], $second[1]];
            }
        }

        return [];
    }
}
