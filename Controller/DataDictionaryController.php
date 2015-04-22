<?php

namespace Rednose\FrameworkBundle\Controller;

use Doctanium\Bundle\CoreBundle\Util\XmlUtil;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Rednose\FrameworkBundle\DataDictionary\DataDictionaryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DataDictionaryController extends Controller
{
    /**
     * @Rest\Post("/dictionaries/{id}/activate", name="rednose_framework_activate_dictionary")
     */
    public function activateDictionaryAction($id)
    {
        $manager = $this->get('rednose_framework.data_dictionary_manager');
        $dictionary = $manager->findDictionaryById($id);

        if ($dictionary === null) {
            throw $this->createNotFoundException();
        }

        $dictionary->getOrganization()->setDataDictionary($dictionary);
        $manager->updateDictionary($dictionary);

        return new Response(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Post("/dictionaries", name="rednose_framework_post_dictionary",  options={"expose"=true})
     * @Rest\Put("/dictionaries/{id}", name="rednose_framework_put_dictionary",  options={"expose"=true})
     *
     * @Rest\QueryParam(name="organization_id", nullable=true, strict=true)
     */
    public function updateDictionaryAction(ParamFetcherInterface $paramFetcher, $id = null)
    {
        $organizationManager = $this->get('rednose_framework.organization_manager');

        // 'organization' param is deprecated, use 'organization_id'
        $organization = $paramFetcher->get('organization_id') ?: $this->get('request')->get('organization');

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

        if ($id === null) {
            $dictionary->setOrganization($organizationManager->findOrganizationById($organization));
        }

        $err = $manager->updateDictionary($dictionary, true);

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
            throw $this->createNotFoundException();
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
     * @Rest\Delete("/dictionaries/{id}", name="rednose_framework_delete_dictionary")
     */
    public function deleteDictionary($id)
    {
        $manager = $this->get('rednose_framework.data_dictionary_manager');
        $dictionary = $manager->findDictionaryById($id);

        if ($dictionary === null) {
            throw $this->createNotFoundException();
        }

        $manager->deleteDictionary($dictionary);

        return new Response(null, Codes::HTTP_NO_CONTENT);
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
     *
     * @Rest\QueryParam(name="organization_id", nullable=false, strict=true)
     */
    public function uploadDictionaryAction(ParamFetcherInterface $paramFetcher)
    {
        $request = $this->get('request');
        $manager = $this->get('rednose_framework.data_dictionary_manager');

        $serializer = $this->get('serializer');

        $context = new DeserializationContext();
        $context->setGroups(array('file'));

        $organizationManager = $this->get('rednose_framework.organization_manager');
        $organization = $organizationManager->findOrganizationById($paramFetcher->get('organization_id'));

        if (!$organization) {
            throw $this->createNotFoundException();
        }

        foreach($request->files as $uploadedFile) {
            if ($uploadedFile instanceof UploadedFile && $uploadedFile->isValid()) {
                $xml = file_get_contents($uploadedFile->getPathname());

                $id = XmlUtil::getId($xml);

                if (!$id || $id === '') {
                    return new Response(null, Codes::HTTP_BAD_REQUEST);
                }

                $context = new DeserializationContext();
                $context->setGroups(array('file'));
                $context->setAttribute('id', $id);

                /** @var DataDictionaryInterface $dictionary */
                $dictionary = $serializer->deserialize($xml, 'Rednose\FrameworkBundle\Entity\DataDictionary', 'xml', $context);
                $dictionary->setOrganization($organization);

                $manager->updateDictionary($dictionary);

                return $this->redirect($this->generateUrl('rednose_framework_get_dictionary', array('id' => $dictionary->getId())));
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
