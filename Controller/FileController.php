<?php

namespace Rednose\FrameworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileController extends Controller
{
    public function uploadAction()
    {
        $request = $this->getRequest();

        if (count($request->files) !== 1) {
            throw new \InvalidArgumentException();
        }

        foreach($request->files as $uploadedFile) {
            if ($uploadedFile instanceof UploadedFile && $uploadedFile->isValid()) {
                $dir  = $this->getTempDir();
                $name = sprintf('%s.tmp', uniqid('tmp'));

                $metadata = array(
                    'name'     => $uploadedFile->getClientOriginalName(),
                    'mimeType' => $uploadedFile->getMimeType(),
                );

                // Write file.
                $file = $uploadedFile->move($dir, $name);

                // Write metadata.
                file_put_contents(sprintf('%s/%s.meta', $dir, $name), serialize($metadata));

                return new Response($file->getFilename());
            }
        }

        return new Response(null, 500);
    }

    protected function getTempDir()
    {
        $base = $this->get('kernel')->getRootDir().'/cache';
        $env  = $this->get('kernel')->getEnvironment();

        return sprintf('%s/%s/rednose_framework/tmp', $base, $env);
    }
}
