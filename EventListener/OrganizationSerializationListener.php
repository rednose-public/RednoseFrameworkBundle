<?php

namespace Rednose\FrameworkBundle\EventListener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Translation\TranslatorInterface;
use Rednose\FrameworkBundle\Model\OrganizationInterface;

class OrganizationSerializationListener implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.post_serialize', 'class' => 'Rednose\FrameworkBundle\Entity\Organization', 'method' => 'onPostSerialize'],
        ];
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var OrganizationInterface $object */
        $object = $event->getObject();
        $visitor = $event->getVisitor();

        if (!$visitor instanceof JsonSerializationVisitor) {
            return;
        }

        $locales = [];

        foreach ($object->getLocalizations() as $localization) {
            $locales[$localization] = Intl::getLocaleBundle()->getLocaleName($localization, $this->translator->getLocale());
        }

        $visitor->addData('locales', $locales);
    }
}
