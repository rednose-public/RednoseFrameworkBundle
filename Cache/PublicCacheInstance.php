<?php

namespace Rednose\FrameworkBundle\Cache;

use Symfony\Component\Routing\RouterInterface;

/**
 * Public cache instance
 */
class PublicCacheInstance extends CacheInstance implements CacheInstanceInterface
{
    /**
     * @var string
     */
    protected $cacheName;

    /**
     * @var string
     */
    protected $publicPath;

    /**
     * Constructor
     *
     * @param string $cacheName
     * @param string $publicPath
     * @param string $rootPath
     * @param RouterInterface $router
     */
    public function __construct($cacheName, $publicPath, $rootPath, RouterInterface $router)
    {
        $this->cacheName  = $cacheName;
        $this->publicPath = str_replace(
            '/app_dev.php', '',
            sprintf('%s/%s', $router->getContext()->getBaseUrl(), $publicPath)
        );

        $rootPath = $this->deductWebServerRoot($rootPath, $this->publicPath);

        parent::__construct($rootPath . '/' . $this->publicPath, $cacheName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicUrl()
    {
        return $this->publicPath . '/' . $this->cacheName;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublic()
    {
        return false;
    }

    /**
     * @param $rootPath
     * @param $publicPath
     */
    protected function deductWebServerRoot($rootPath, $publicPath)
    {
        // Find the webserver root
        $rootPath   = $rootPath . '/';
        $publicPath = explode('/', $this->publicPath);

        foreach ($publicPath as $folder) {
            $rootPath = str_replace('/' . $folder . '/', '', $rootPath);
        }

        return $rootPath;
    }
}
