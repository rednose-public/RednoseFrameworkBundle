<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle;

use Rednose\FrameworkBundle\DependencyInjection\Compiler\AdminPoolCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The general RedNose Framework.
 */
class RednoseFrameworkBundle extends Bundle
{
    /**
     * Current Bundle version.
     *
     * @var string
     */
    const VERSION = '1.7';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
    }
}
