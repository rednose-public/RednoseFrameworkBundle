<?php

namespace Rednose\FrameworkBundle\Controller;

use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes;

class DataDictionaryController extends Controller
{
    /**
     * @Rest\Get("/dictionaries", name="rednose_framework_get_dictionaries", options={"expose"=true})
     */
    public function getDictionariesAction()
    {
        $dictionaries = $this->get('rednose_framework.data_dictionary_manager')->findDictionaries();

        $context = new SerializationContext();
        $context->setGroups('list');

        $view = new View();
        $view->setSerializationContext($context);
        $view->setData($dictionaries);
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Rest\Get("/dictionaries/{id}", name="rednose_framework_get_dictionary", options={"expose"=true})
     */
    public function getDictionaryAction($id)
    {
        $manager = $this->get('rednose_framework.data_dictionary_manager');

        $dictionary = $manager->findDictionaryById($id);

        if ($dictionary === null) {
            return new Response(null, Codes::HTTP_NOT_FOUND);
        }

        $context = new SerializationContext();
        $context->setGroups('details');

        $view = new View();
        $view->setSerializationContext($context);
        $view->setData($dictionary);
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Rest\Get("/dictionaries/{id}/tree", name="rednose_framework_get_dictionary_tree", options={"expose"=true})
     */
    public function getDictionaryTreeAction($id)
    {
        $manager = $this->get('rednose_framework.data_dictionary_manager');

        $dictionary = $manager->findDictionaryById($id);

        if ($dictionary === null) {
            return new Response(null, Codes::HTTP_NOT_FOUND);
        }

        $context = new SerializationContext();
        $context->setGroups('details');

        $view = new View();
        $view->setSerializationContext($context);
        $view->setData($dictionary->toArray());
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
