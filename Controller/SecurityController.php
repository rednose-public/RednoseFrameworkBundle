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

use  Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        $userManager = $this->get('rednose_framework.user_manager');

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

        $csrfToken = $this->container->get('security.csrf.token_manager')->getToken('authenticate');

        return $this->render('RednoseFrameworkBundle:Security:login.html.twig', array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
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
}
