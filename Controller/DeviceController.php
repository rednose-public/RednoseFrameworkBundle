<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Rednose\FrameworkBundle\Entity\Device;

class DeviceController extends Controller
{
    public function iosSubscribeAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $identifier = $this->get('request')->request->get('device_identifier');
        $token      = $this->get('request')->request->get('device_token');
        $user       = $this->get('security.context')->getToken()->getUser();

        $device = $em->getRepository('Rednose\FrameworkBundle\Entity\Device')->findOneBy(array('user' => $user, 'identifier' => $identifier));

        if ($device === null) {
            $device = new Device;

            $device->setType(Device::TYPE_IOS);

            $device->setUser($user);
            $device->setIdentifier($identifier);
        }

        $device->setToken($token);

        $em->persist($device);
        $em->flush();

        return new Response();
    }

    public function iosUnsubscribeAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $identifier = $this->get('request')->request->get('device_identifier');
        $user       = $this->get('security.context')->getToken()->getUser();

        $devices = $em->getRepository('Rednose\FrameworkBundle\Entity\Device')->findBy(array('user' => $user, 'identifier' => $identifier));

        foreach ($devices as $device) {
            $em->remove($device);
        }

        $em->flush();

        return new Response();
    }
}
