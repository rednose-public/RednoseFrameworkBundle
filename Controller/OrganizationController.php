<?php

namespace Rednose\FrameworkBundle\Controller;

use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends Controller
{
    /**
     * @Rest\Get("/organizations", name="rednose_docgen_get_organizations", options={"expose"=true})
     */
    public function getOrganizationsAction()
    {
        $organizations = $this->get('rednose_framework.organization_manager')->findOrganizations();

        $context = new SerializationContext();
        $context->setGroups('list');

        $view = new View();
        $view->setSerializationContext($context);
        $view->setData($organizations);
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
