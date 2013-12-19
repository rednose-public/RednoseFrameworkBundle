<?php

namespace Rednose\FrameworkBundle\Purifier;

use HTMLPurifier;
use HTMLPurifier_Bootstrap;
use HTMLPurifier_Config;

/**
 * Factory class that constructs a configured HTML purifier instance.
 */
class PurifierFactory
{
    /**
     * Creates and returns a purifier instance.
     *
     * @return HTMLPurifier
     */
    public function create()
    {
        $cache = sys_get_temp_dir() . '/fgHtmlPurify';

        if (is_dir($cache) === false) {
            mkdir($cache, 0777, true);
            umask(umask(0));
        }

        $config = HTMLPurifier_Config::createDefault();

        $config->set('Cache.SerializerPath', $cache);
        $config->set('CSS.AllowedProperties', array());

        $def = $config->getHTMLDefinition(true);

        // Add textblock placeholder attribute to allowed definitions.
        $def->addAttribute('span', 'data-placeholder', 'Text');

        return new HTMLPurifier($config);
    }
}
