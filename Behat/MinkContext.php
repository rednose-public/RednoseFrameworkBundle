<?php

namespace Rednose\FrameworkBundle\Behat;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class MinkContext extends \Behat\MinkExtension\Context\MinkContext implements KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^I am logged in as "([^"]*)"$/
     */
    public function iAmLoggedInAs($username)
    {
        $session = $this->getContainer()->get('session');

        $user = $this->getContainer()->get('rednose_framework.user_manager')->findUserByUsername($username);
        $providerKey = $this->getContainer()->getParameter('fos_user.firewall_name');

        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $session->set('_security_'.$providerKey, serialize($token));

        // Assume 'user' firewall context as well
        $session->set('_security_user', serialize($token));

        $session->save();

        // Hack for Selenium
        // Before setting a cookie the browser needs to be launched
        if ($this->getSession()->getDriver() instanceof \Behat\Mink\Driver\Selenium2Driver) {
            $this->visit('login');
        }

        // Set the cookie
        $minkSession = $this->getSession();
        $minkSession->setCookie($session->getName(), $session->getId());
    }

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

    /**
     * Returns Container instance.
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }
}
