<?php

namespace Rednose\FrameworkBundle\Controller;

use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes;

class DataDictionaryController extends Controller
{
    /**
     * @Rest\Post("/dictionaries", name="rednose_framework_post_dictionary",  options={"expose"=true})
     * @Rest\Put("/dictionaries/{id}", name="rednose_framework_put_dictionary",  options={"expose"=true})
     */
    public function updateDictionaryAction($id = null)
    {
        $serializer = $this->get('serializer');
        $manager = $this->get('rednose_framework.data_dictionary_manager');
        $request = $this->getRequest();

        $context = new DeserializationContext();
        $context->setGroups(array('details'));

        if ($id !== null) {
            $dictionary = $manager->findDictionaryById($id);

            if ($dictionary === null) {
                throw $this->createNotFoundException();
            }

            $context->setAttribute('id', $dictionary->getId());
        }

        $dictionary = $serializer->deserialize($request->getContent(), 'Rednose\FrameworkBundle\Entity\DataDictionary', 'json', $context);

        $manager->updateDictionary($dictionary, true);

        return new JsonResponse(array('id' => $dictionary->getId()), Codes::HTTP_CREATED);
    }

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

        $context = new SerializationContext();
        $context->setGroups('details');

        $view = new View();
        $view->setSerializationContext($context);
        $view->setData($dictionary->toArray());
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Rest\Get("/dictionaries/{id}/list", name="rednose_framework_get_dictionary_list", options={"expose"=true})
     */
    public function getDictionaryTreeControlAction($id)
    {
        $manager = $this->get('rednose_framework.data_dictionary_manager');
        $dictionary = $manager->findDictionaryById($id);

        $types = array();

        if ($this->getRequest()->query->get('type')) {
            $types = explode(',', $this->getRequest()->query->get('type'));
        }

        $context = $this->getRequest()->query->get('context');

        $view = new View();
        $view->setData($dictionary->toList($types, $context));
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
