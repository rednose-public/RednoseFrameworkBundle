<?php

namespace Rednose\FrameworkBundle\Behat;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;

class JavaScriptMinkContext extends \Behat\MinkExtension\Context\MinkContext
{
    /**
     * {@inheritdoc}
     */
    public function fillField($field, $value)
    {
        $this->waitForAngular();

        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);

        $element = $this->getSession()->getPage()->findField($field);

        if (null === $element) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), 'form field', 'id|name|label|value|placeholder', $element);
        }

        if ($element->getAttribute('rn-autocomplete') === null) {
            parent::fillField($field, $value);

            return;
        }

        $this->setAutocompleteValue($element, $value);
    }

    /**
     * @param NodeElement $element
     * @param string      $value
     */
    public function setAutoCompleteValue(NodeElement $element, $value)
    {
        $this->getSession()->getDriver()->focus($element->getXpath());
        $this->getSession()->evaluateScript('$("#'.$element->getAttribute('id').'").typeahead("val", "'.$value.'");');
    }

    protected function waitForAngular()
    {
        if ($this->getSession()->evaluateScript('return (typeof angular !== \'undefined\')')) {
            $this->getSession()->evaluateScript(
                'angular.getTestability(document.body).whenStable(function() {
                    window.__testable = true;
                })');

            $this->getSession()->wait(5000, 'window.__testable === true');
        }

        if ($this->getSession()->evaluateScript('return (typeof jQuery != \'undefined\')')) {
            $this->getSession()->wait(5000, '(0 === jQuery.active && 0 === jQuery(\':animated\').length)');
        }
    }
}
