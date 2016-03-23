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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        // Form authentication
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
        }

        $headers = [];

        if ($request->isXmlHttpRequest()) {
            $headers['REQUIRES_AUTH'] =  '1';
        }

        $csrfToken = $this->container->get('security.csrf.token_manager')->getToken('authenticate');

        return $this->render('RednoseFrameworkBundle:Security:login.html.twig', array(
            'last_username' => $request->getSession()->get(Security::LAST_USERNAME),
            'csrf_token'    => $csrfToken,
            'error'         => $error
        ), new Response('', 200, $headers));
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
