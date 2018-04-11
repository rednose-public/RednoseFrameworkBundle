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
use FOS\RestBundle\Controller\Annotations as Rest;

use Rednose\FrameworkBundle\Model\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User API Controller
 */
class UserController extends Controller
{
    /**
     * Returns information about the current user.
     *
     * @Rest\Get("/user", name="rednose_framework_get_user")
     *
     * @param Request $request
     * @return Response
     */
    public function getUserAction(Request $request)
    {
        /** @var UserInterface $user */
        $user    = $this->get('security.token_storage')->getToken()->getUser();
        $manager = $this->get('rednose_framework.organization_manager');

        $locale       = $user->getLocale() ?: $request->getLocale();
        $organization = $user->getOrganization() ?: $manager->findOrganizationBy(array());

        $data = [
            'id'           => $user->getId(),
            'username'     => $user->getUsername(),
            'bestname'     => $user->getBestname(),
            'email'        => $user->getEmail(),
            'locale'       => $locale,
            'organization' => $organization ? $organization->getId() : null,
        ];

        $view = new View();
        $view->setData($data);
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Update current user properties.
     * Currently supported: locale, organization
     *
     * @Rest\Post("/user", name="rednose_framework_set_user")
     *
     * @param Request $request
     * @return Response
     */
    public function postUserAction(Request $request)
    {
        /** @var UserInterface $user */
        $user   = $this->get('security.token_storage')->getToken()->getUser();
        $params = json_decode($request->getContent(), true);

        // Set user locale
        if (array_key_exists('locale', $params)) {
            $user->setLocale($params['locale']);
        }

        // Set user organization
        if (array_key_exists('organization', $params)) {
            $manager      = $this->get('rednose_framework.organization_manager');
            $organization = $manager->findOrganizationById($params['organization']);

            if (!$organization) {
                throw $this->createNotFoundException('Organization not found');
            }

            $user->setOrganization($organization);
        }

        $this->get('rednose_framework.user_manager')->updateUser($user);

        return $this->redirect($this->generateUrl('rednose_framework_get_user'));
    }
}
