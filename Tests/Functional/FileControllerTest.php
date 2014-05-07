<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileControllerTest extends WebTestCase
{
    public function setUp()
    {
        copy(__DIR__.'/../Fixtures/files/test.txt', sys_get_temp_dir().'/test.txt');
        copy(__DIR__.'/../Fixtures/files/test.pdf', sys_get_temp_dir().'/test.pdf');
    }

    /**
     * @dataProvider getFileData
     */
    public function testUploadFile($path, $name, $mimeType)
    {
        $client   = $this->getAdminClient();
        $file     = new UploadedFile($path, $name, $mimeType, strlen(file_get_contents($path)));
        $contents = file_get_contents($path);

        $client->request(
            'POST',
            '/files/upload',
            array(),
            array('file' => $file)
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($contents, file_get_contents(sprintf('%s/%s', $this->getTempDir(), $client->getResponse()->getContent())));
    }

    public function getFileData()
    {
        return array(
            array(sys_get_temp_dir().'/test.txt', 'test.txt', 'text/plain'),
            array(sys_get_temp_dir().'/test.pdf', 'test.pdf', 'application/pdf'),
        );
    }

    private function getAdminClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));
    }

    private function getTempDir()
    {
        $client = static::createClient();

        $base = $client->getContainer()->get('kernel')->getRootDir().'/cache';
        $env  = $client->getContainer()->get('kernel')->getEnvironment();

        return sprintf('%s/%s/rednose_framework/tmp', $base, $env);
    }
}
