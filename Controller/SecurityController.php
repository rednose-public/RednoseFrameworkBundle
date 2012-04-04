<?php

namespace Libbit\FrameworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function indexAction()
    {
        $request = $this->get('request');
        
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
        
        return $this->render('LibbitFrameworkBundle:Security:login.html.twig', array(
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'csrf_token' => $csrfToken,
            'error' => $error
        ));
    }
    
    public function logoutAction()
    {
        
    }
}
