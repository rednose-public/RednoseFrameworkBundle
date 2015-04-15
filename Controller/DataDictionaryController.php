<?php

namespace Rednose\FrameworkBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DataDictionaryController extends Controller
{
    /**
     * @Rest\Post("/dictionaries", name="rednose_framework_post_dictionary",  options={"expose"=true})
     * @Rest\Put("/dictionaries/{id}", name="rednose_framework_put_dictionary",  options={"expose"=true})
     */
    public function updateDictionaryAction($id = null)
    {
        $organization = $this->get('request')->get('organization');

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

        $err = $manager->updateDictionary($dictionary, true);

        if ($err !== true) {
            return new JsonResponse(array('message' => (string)$err), Codes::HTTP_BAD_REQUEST);
        }

        // Bind the dictionary to a supplied organization. Warning: existing connection will be lost.
        if ($organization !== null) {
            $organizationManager = $this->get('rednose_framework.organization_manager');

            if ($organization = $organizationManager->findOrganizationById($organization)) {
                $organization->setDataDictionary($dictionary);

                $organizationManager->updateOrganization($organization);
            }
        }

        return new JsonResponse(array('id' => $dictionary->getId()), Codes::HTTP_CREATED);
    }

    /**
     * @Rest\Get("/dictionaries", name="rednose_framework_get_dictionaries", options={"expose"=true})
     *
     * @Rest\QueryParam(name="organization_id", nullable=true, strict=true)
     */
    public function getDictionariesAction(ParamFetcherInterface $paramFetcher)
    {
        // 'organization' param is deprecated, use 'organization_id'
        $organization = $paramFetcher->get('organization_id') ?: $this->get('request')->get('organization');

        if ($organization !== null) {
            $organization = $this->get('rednose_framework.organization_manager')->findOrganizationById($organization);
        }

        $dictionaries = $this->get('rednose_framework.data_dictionary_manager')->findDictionaries($organization);

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
     * @Rest\Get("/dictionaries/{id}/download", name="rednose_framework_download_dictionary", options={"expose"=true})
     */
    public function downloadDictionaryAction($id)
    {
        $manager = $this->get('rednose_framework.data_dictionary_manager');

        $dictionary = $manager->findDictionaryById($id);

        if ($dictionary === null) {
            return new Response(null, Codes::HTTP_NOT_FOUND);
        }

        $serializer = $this->get('serializer');

        $context = new SerializationContext();
        $context->setGroups(array('file'));

        $content = $serializer->serialize($dictionary, 'xml', $context);

        $response = new Response();
        $response->setContent($content);
        $response->headers->add(array(
            'Content-Type'        => 'application/xml',
            'Content-Length'      => strlen($content),
            'Content-Disposition' => 'attachment; filename="' . $dictionary->getName() . '.xml"',
        ));

        return $response;
    }

    /**
     * @Rest\Post("/dictionaries/upload", name="rednose_dictionary_upload", options={"expose"=true})
     */
    public function uploadDictionaryAction()
    {
        $request = $this->get('request');
        $manager = $this->get('rednose_framework.data_dictionary_manager');

        $serializer = $this->get('serializer');

        $context = new DeserializationContext();
        $context->setGroups(array('file'));

        $organization = $this->get('request')->get('organization');

        if (!$organization) {
            return new Response('', Codes::HTTP_BAD_REQUEST);
        }

        if ($file = $request->files->get('assetFile')) {
            if ($file->getClientOriginalExtension() === 'xml') {
                if ($xml = file_get_contents($file->getPathname())) {
                    if ($object = $serializer->deserialize($xml, 'Rednose\FrameworkBundle\Entity\DataDictionary', 'xml', $context)) {
                        // Bind the dictionary to a supplied organization. Warning: existing connection will be lost.
                        if ($organization !== null) {
                            $organizationManager = $this->get('rednose_framework.organization_manager');

                            if ($organization = $organizationManager->findOrganizationById($organization)) {
                                $err = $manager->updateDictionary($object, true);

                                if ($err !== true) {
                                    return new Response((string) $err, Codes::HTTP_BAD_REQUEST);
                                }

                                $organization->setDataDictionary($object);

                                $organizationManager->updateOrganization($organization);
                            } else {
                                return new Response('Dictionary not found.', Codes::HTTP_NOT_FOUND);
                            }
                        }

                        return new Response($object->getId());
                    }
                }
            }
        }

        return new Response('', Codes::HTTP_BAD_REQUEST);
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
