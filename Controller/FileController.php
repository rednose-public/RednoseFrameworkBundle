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
                $name = sprintf('%s.%s', uniqid(), $uploadedFile->getClientOriginalExtension());

                $metadata = array(
                    'name'     => $uploadedFile->getClientOriginalName(),
                    'mimeType' => $uploadedFile->getMimeType(),
                );

                // Write file.
                $uploadedFile->move($dir, $name);

                // Write metadata.
                file_put_contents(sprintf('%s/%s.meta', $dir, $name), serialize($metadata));

                $uri = sprintf('%s/%s', $dir, $name);

                return new Response(str_replace('/app_dev.php', '', sprintf('%s/%s', $this->container->get('router')->getContext()->getBaseUrl(), $uri)));
            }
        }

        return new Response(null, 500);
    }

    /**
     * @return string
     */
    protected function getTempDir()
    {
        return sprintf('cache/rednose_framework/file');
    }
}
