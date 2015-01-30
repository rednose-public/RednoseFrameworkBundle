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
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginAction()
    {
        $userManager = $this->get('rednose_framework.user_manager');
        $request = $this->get('request');

        // Saml authentication
        if ($this->container->getParameter('rednose_framework.use_saml') === true) {
            return $this->redirect($this->generateUrl('aerial_ship_saml_sp.security.login'));
        }

        // Token authentication
        if ($userManager->tokenAuthentication($this->container)) {
            if ($request->get('redirect')) {
                return $this->redirect($request->get('redirect'));
            }

            if ($request->get('route')) {
                return $this->redirect($this->generateUrl($request->get('route')));
            }
        }

        // Form authentication
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');

        return $this->render('RednoseFrameworkBundle:Security:login.html.twig', array(
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'csrf_token'    => $csrfToken,
            'error'         => $error
        ));
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

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
