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

    /**
     * @Get("/forms/{id}/preview", name="rednose_framework_forms_preview", options={"expose"=true})
     */
    public function previewAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $model = $em->getRepository('Rednose\FrameworkBundle\Entity\Form')->findOneById($id);

        if (!$model) {
            throw $this->createNotFoundException();
        }

        // FIXME: Form should be able to construct without data: use source form as config option.
        $form = $this->createForm('content_section', null, array(
            'form' => $model,
        ));

        return $this->render('RednoseFrameworkBundle:Form:preview.html.twig', array(
            'model' => $model,
            'form'  => $form->createView(),
        ));
    }
}
