<?php

namespace Rednose\FrameworkBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;

/**
 * Injects the headers needed for a file download.
 *
 * @author Marc Bontje <marc@rednose.nl>
 */
class DownloadResponse extends Response
{
    /**
     * Constructor
     *
     * @param string $content  The binary file contents
     * @param string $filename The file name
     * @param string $mimetype Mimetype
     */
    public function __construct($content = '', $filename = '', $mimetype = 'application/octet-stream')
    {
        $headers = array(
            'Content-Type' => $mimetype,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        );

        parent::__construct($content, 200, $headers);
    }
}
