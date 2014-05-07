<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Purifier;

use HTMLPurifier_Config;
use HTMLPurifier_Lexer;
use HTMLPurifier;

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

        // Fixes encoding.
        $config->set('Core.EscapeNonASCIICharacters', true);

        $def = $config->getHTMLDefinition(true);

        // Add textblock placeholder attribute to allowed definitions.
        $def->addAttribute('span', 'data-placeholder', 'Text');
        $def->addAttribute('span', 'data-header-target', 'Text');

        return new RednoseHTMLPurifier($config);
    }

    /**
     * Creates and returns a purifier lexer instance.
     *
     * @return HTMLPurifier_Lexer
     */
    public function createLexer()
    {
        $cache = sys_get_temp_dir() . '/fgHtmlPurify';

        if (is_dir($cache) === false) {
            mkdir($cache, 0777, true);
            umask(umask(0));
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', $cache);

        return HTMLPurifier_Lexer::create($config);
    }
}
