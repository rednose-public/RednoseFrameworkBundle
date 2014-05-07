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

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The general RedNose Framework.
 *
 * @author Marc Bontje <marc@rednose.nl>
 * @author Sven Hagemann <sven@rednose.nl>
 */
class RednoseFrameworkBundle extends Bundle
{
    /**
     * Current Bundle version.
     *
     * @var string
     */
    const VERSION = '1.3';
}
