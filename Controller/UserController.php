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

use FOS\RestBundle\View\View;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * User API Controller
 */
class UserController extends Controller
{
    /**
     * Returns information about the current user.
     */
    public function getUserAction()
    {
        /** @var UserInterface $user */
        $user    = $this->get('security.context')->getToken()->getUser();
        $request = $this->getRequest();
        $manager = $this->get('rednose_framework.organization_manager');

        $locale       = $user->getLocale() ?: $request->getLocale();
        $organization = $user->getOrganization() ?: $manager->findOrganizationBy(array());

        $data = array(
            'id'           => $user->getId(),
            'username'     => $user->getUsername(),
            'bestname'     => $user->getBestname(),
            'email'        => $user->getEmail(),
            'locale'       => $locale,
            'organization' => $organization ? $organization->getId() : null,
        );

        $view = new View();
        $view->setData($data);
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function postUserAction()
    {
        /** @var UserInterface $user */
        $user   = $this->get('security.context')->getToken()->getUser();
        $params = json_decode($this->getRequest()->getContent(), true);

        if (array_key_exists('locale', $params)) {
            $user->setLocale($params['locale']);
        }

        if (array_key_exists('organization', $params)) {
            $organization = $this->get('rednose_framework.organization_manager')->findOrganizationById($params['organization']);
            $user->setOrganization($organization);
        }

        $this->get('rednose_framework.user_manager')->updateUser($user);

        return $this->redirect($this->generateUrl('rednose_framework_get_user'));
    }
}
