<?php

namespace Rednose\FrameworkBundle\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrganizationController extends Controller
{
    /**
     * @Rest\Get("/organizations", name="rednose_framework_get_organizations", options={"expose"=true})
     */
    public function getOrganizationsAction()
    {
        $organizations = $this->get('rednose_framework.organization_manager')->findOrganizations();

        if ($this->get('security.token_storage')->getToken()) {
            $user = $this->get('security.token_storage')->getToken()->getUser();

            if ($user->hasRole('ROLE_ORGANIZATION_ADMIN')) {
                $organizations = [$user->getOrganization()];
            }
        }

        $context = new Context();
        $context->addGroup('list');

        $view = new View();
        $view->setContext($context);
        $view->setData($organizations);
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
