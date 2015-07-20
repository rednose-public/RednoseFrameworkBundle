<?php

namespace Rednose\FrameworkBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrganizationController extends Controller
{
    /**
     * @Rest\Get("/organizations", name="rednose_framework_get_organizations", options={"expose"=true})
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

    /**
     * @Rest\Get("/organizations/{id}/switch", name="rednose_framework_switch_organization")
     */
    public function switchOrganizationAction($id)
    {
        $organization = $this->get('rednose_framework.organization_manager')->findOrganizationById($id);

        if ($organization === null) {
            throw $this->createNotFoundException('Invalid organization');
        }

        /** @var UserInterface $user */
        $user = $this->get('security.context')->getToken()->getUser();
        $user->setOrganization($organization);

        $this->get('rednose_framework.user_manager')->updateUser($user);

        return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
    }
}
