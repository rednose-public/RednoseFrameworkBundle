<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Form\DataTransformer;

use Rednose\FrameworkBundle\Entity\File;
use Rednose\FrameworkBundle\Model\FileInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileTransformer implements DataTransformerInterface
{
    /**
     * @var FileInterface
     */
    protected $file;

    /**
     * @param FileInterface $file
     *
     * @return null
     */
    public function transform($file)
    {
        $this->file = $file;

        return null;
    }

    /**
     * @param UploadedFile|FileInterface $uploadedFile
     *
     * @return FileInterface
     */
    public function reverseTransform($uploadedFile)
    {
        if (!$uploadedFile instanceof UploadedFile || !$uploadedFile->isValid()) {
            return $this->file;
        }

        $file = new File();

        $file->setName($uploadedFile->getClientOriginalName());
        $file->setMimeType($uploadedFile->getMimeType());

        $file->setDateCreated(new \DateTime());
        $file->setDateModified(new \DateTime());

        $file->setContent(file_get_contents($uploadedFile->getRealPath()));

        return $file;
    }
}
