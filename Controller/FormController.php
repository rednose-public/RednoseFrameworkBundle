<?php

namespace Rednose\FrameworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View;

class FormController extends Controller
{
    /**
     * @Get("/forms/{id}/read", name="rednose_framework_forms_read", options={"expose"=true})
     */
    public function readAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $form = $em->getRepository('Rednose\FrameworkBundle\Entity\Form')->findOneById($id);

        if (!$form) {
            throw $this->createNotFoundException();
        }

        $view = View::create();
        $view->setFormat('json');
        $view->setData($form);

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
