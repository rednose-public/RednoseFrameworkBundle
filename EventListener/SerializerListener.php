<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\EventListener;

use Rednose\FrameworkBundle\Serializer\Handler\ArrayCollectionHandler;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

use Doctrine\Bundle\DoctrineBundle\Registry;

class SerializerListener implements EventSubscriberInterface
{
    /**
     * @var {ArrayCollectionHandler}
     */
    protected $handler = null;

    public function __construct(ArrayCollectionHandler $handler, Registry $managerRegistry)
    {
        $this->handler = $handler;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_deserialize', 'method' => 'onPostDeserialize'),
        );
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        if ($event->getContext()->getDepth() === 0) {
            $queue = $this->handler->getCollectionsTransactionQueue();

            if ($queue !== false) {

                foreach ($queue->movedItems as $item) {
                    $item['oldCollection']->removeItem($item['entity']);
                    $item['newCollection']->add($item['entity']);
                }

            }
        }
    }
}

