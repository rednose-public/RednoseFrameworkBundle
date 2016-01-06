<?php

namespace Rednose\FrameworkBundle\Model;

interface FileInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $content
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated);

    /**
     * @return \DateTime
     */
    public function getDateCreated();

    /**
     * @param \DateTime $dateModified
     */
    public function setDateModified($dateModified);

    /**
     * @return \DateTime
     */
    public function getDateModified();

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType);

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * Get temp directory
     *
     * @return string
     */
    public function getTempDir();

    /**
     * @return string
     */
    public function getExtension();

    /**
     * @param string $extension
     */
    public function setExtension($extension);
}