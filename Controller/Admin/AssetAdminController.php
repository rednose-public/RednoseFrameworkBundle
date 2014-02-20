<?php

namespace Rednose\FrameworkBundle\Controller\Admin;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Serializer;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AssetAdminController extends Controller
{
    public function uploadAction()
    {
        $form = $this->createFormBuilder()
            ->add('file', 'file')
            ->getForm();

        if ($this->getRestMethod() == 'POST') {
            $form->bind($this->getRequest());

            $key          = $form['file'];
            $uploadedfile = $key->getData();

            if ($uploadedfile instanceof UploadedFile) {
                $file = $uploadedfile->move(sys_get_temp_dir());

                $info = new SplFileInfo($file->getRealPath(), null, null);

                $extension = $uploadedfile->getClientOriginalExtension();

                if ($extension === 'xml') {
                    $this->importXml($info);
                } elseif ($extension === 'zip') {
                    $this->importZip($info);
                } else {
                    throw new \RuntimeException('Invalid file type.');
                }
            }

            $this->addFlash('sonata_flash_success', $this->admin->trans('flash_upload_success', array(), 'SonataAdminBundle'));

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render('RednoseFrameworkBundle:AssetAdmin:upload.html.twig', array(
            'action' => 'edit',
            'form'   => $view,
        ));
    }

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

        foreach ($selectedModelQuery->execute() as $object) {
            $context = new SerializationContext();
            $context->setGroups(array('file'));

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

    private function importXml(SplFileInfo $file)
    {
        $context = new DeserializationContext();
        $context->setGroups(array('file'));

        $object = $this->getSerializer()->deserialize($file->getContents(), $this->admin->getClass(), 'xml', $context);

        $this->admin->update($object);
    }

    private function importZip(SplFileInfo $file)
    {
        $tmpDir = sys_get_temp_dir().'/'.uniqid();

        $zip = new \ZipArchive();
        $zip->open($file->getRealPath());

        $zip->extractTo($tmpDir);
        $zip->close();

        $finder = new Finder();
        $finder->files()->in($tmpDir);

        foreach ($finder as $file) {
            if (!$file instanceof SplFileInfo) {
                continue;
            }

            if ($file->getExtension() === 'xml') {
                $this->importXml($file);
            }
        }
    }
}
