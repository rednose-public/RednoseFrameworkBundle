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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Rednose\FrameworkBundle\Model\Form;

class FormController extends Controller
{
    /**
     * @Route("/forms/{id}/read", name="rednose_framework_forms_read", options={"expose"=true})
     * @Method({"GET"})
     */
    public function readAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $form = $em->getRepository('Rednose\FrameworkBundle\Entity\Form')->findOneById($id);

        if (!$form) {
            throw $this->createNotFoundException();
        }

        $context = new SerializationContext();
        $context->setGroups(array('details'));

        $view = View::create();
        $view->setSerializationContext($context);
        $view->setFormat('json');
        $view->setData($form);

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/forms/{id}/preview", name="rednose_framework_forms_preview", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function previewAction($id)
    {
        $em         = $this->get('doctrine.orm.entity_manager');
        $serializer = $this->get('serializer');

        /** @var Form $model */
        $model = $em->getRepository('Rednose\FrameworkBundle\Entity\Form')->findOneById($id);

        if (!$model) {
            throw $this->createNotFoundException();
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        $form = $dom->createElement('form');
        $test = $dom->createElement('test');
        $form->appendChild($test);
        $dom->appendChild($form);

        $form = $this->createForm('rednose_form', $dom, array(
            'form' => $model,
        ));

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $encoder = new XmlEncoder($model->getName());
            $data    = $form->getData();

            return new Response($encoder->encode($data, 'xml'), 200, array(
                'Content-Type' => 'application/xml',
            ));
        }

        $context = new SerializationContext();
        $context->setGroups('details');

        return $this->render('RednoseFrameworkBundle:Form:preview.html.twig', array(
            'model' => $model,
            'json'  => $serializer->serialize($model, 'json', $context),
            'form'  => $form->createView(),
        ));
    }

    /**
     * @Route("/forms/{id}/export", name="rednose_framework_forms_export", options={"expose"=true})
     * @Method({"GET"})
     */
    public function exportAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $form = $em->getRepository('Rednose\FrameworkBundle\Entity\Form')->findOneById($id);

        if (!$form) {
            throw $this->createNotFoundException();
        }

        $context = new SerializationContext();
        $context->setGroups(array('file'));

        $view = View::create();
        $view->setSerializationContext($context);
        $view->setFormat('xml');
        $view->setData($form);

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
