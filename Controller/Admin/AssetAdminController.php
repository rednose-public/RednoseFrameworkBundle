<?php

namespace Rednose\FrameworkBundle\Controller\Admin;

use JMS\Serializer\SerializationContext;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Serializer;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class AssetAdminController extends Controller
{
    public function downloadAction($id = null)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        $context = new SerializationContext();
        $context->setGroups(array('file'));

        $content = $this->getSerializer()->serialize($object, 'xml', $context);

        $response = new Response;
        $response->setContent($content);
        $response->headers->add(array(
            'Content-Type'        => 'application/xml',
            'Content-Length'      => strlen($content),
            'Content-Disposition' => 'attachment; filename="'.$object->getForeignId().'"',
        ));

        return $response;
    }

    public function batchActionDownload(ProxyQueryInterface $selectedModelQuery)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $archive = sys_get_temp_dir().'/'.'assets.zip';

        $zip = new \ZipArchive();
        $zip->open($archive, \ZipArchive::CREATE);

        $context = new SerializationContext();
        $context->setGroups(array('file'));

        foreach ($selectedModelQuery->execute() as $object) {
            $zip->addFromString($object->getForeignId().'.xml', $this->getSerializer()->serialize($object, 'xml', $context));
        }

        $zip->close();

        $content = file_get_contents($archive);
        unlink($archive);

        $response = new Response;
        $response->setContent($content);
        $response->headers->add(array(
            'Content-Type'        => 'application/zip',
            'Content-Length'      => strlen($content),
            'Content-Disposition' => 'attachment; filename="assets"',
        ));

        return $response;
    }

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        return $this->get('serializer');
    }
}
