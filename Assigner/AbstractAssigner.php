<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Assigner;

use Rednose\FrameworkBundle\Context\SessionContext;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class AbstractAssigner
{
    /**
     * Test if atleast one assignment condition evaluates to true
     *
     * You can supply multiple priority arrays, the condition with the highest priority
     * will be returned as a match.
     *
     * In case of multiple matches with the same priority the first match will be returned
     *
     * @param string                            $username
     * @param array                             $prioArrays
     * @param SessionContext                    $context
     * @param ExpressionLanguage                $language
     *
     * @return array|null
     */
    protected function shouldAssignPrioritized($username, $prioArrays, SessionContext $context, ExpressionLanguage $language)
    {
        $highestPriority = 0;
        $matchingConditions = [];

        foreach ($prioArrays as $source => $prioritizedArray) {
            foreach ($prioritizedArray as $condition) {

                try {
                    if ($language->evaluate($condition['conditions'], $context->get($username))) {
                        $condition['source'] = $source;

                        $matchingConditions[] = $condition;

                        if ($highestPriority <= $condition['priority']) {
                            $highestPriority = $condition['priority'];
                        }
                    }
                } catch (\Exception $e) {}
            }
        }

        foreach ($matchingConditions as $match) {
            if ($match['priority'] === $highestPriority) {
                return $match;
            }
        }

        return null;
    }

}