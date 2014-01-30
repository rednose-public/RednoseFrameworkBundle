<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use HTMLPurifier;

class EditorTypeDataListener implements EventSubscriberInterface
{
    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    public function __construct(HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'setData');
    }

    /**
     * {@inheritdoc}
     */
    public function setData(FormEvent $event)
    {
        $data = $event->getData();

        if ($data === null) {
            return;
        }

        $event->setData($this->purifier->purify($data));
    }
}
