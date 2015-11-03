<?php

namespace Rednose\FrameworkBundle\Twig\Extension;

use Rednose\FrameworkBundle\Model\FileInterface;
use Rednose\FrameworkBundle\Thumbnail\ThumbnailGenerator;

class ThumbnailExtension extends \Twig_Extension
{
    /**
     * @var ThumbnailGenerator
     */
    protected $generator;

    /**
     * @param ThumbnailGenerator $generator
     */
    public function __construct(ThumbnailGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'rednose_framework_url' => new \Twig_Filter_Method($this, 'thumbnail')
        );
    }

    public function thumbnail(FileInterface $file)
    {
        return $this->generator->getFileUrl($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rednose_framework_url';
    }
}
