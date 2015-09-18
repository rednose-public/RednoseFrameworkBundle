<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AdminPoolCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sonata.admin.pool')) {
            return;
        }

        $definition = $container->getDefinition('sonata.admin.pool');
        $definition->setClass('Rednose\FrameworkBundle\Admin\OrganizationAwarePool');
    }
}