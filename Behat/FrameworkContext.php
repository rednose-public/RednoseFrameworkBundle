<?php

namespace Rednose\FrameworkBundle\Behat;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\Context;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FrameworkContext extends RawMinkContext implements Context, KernelAwareContext
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
     * @Then /^I wait 1 second$/
     */
    public function iWait1Second()
    {
        $this->getSession()->wait(1000);
    }

    /**
     * @Then /^I wait 1 hour$/
     */
    public function iWait1Hour()
    {
        $this->getSession()->wait(3600000);
    }

    /**
     * @Then /^I wait$/
     */
    public function iWait()
    {
        $this->getSession()->wait(5000);
    }

    /**
     * @Given /^I am logged in as administrator$/
     */
    public function iAmLoggedInAsAdministrator()
    {
        $this->iAmLoggedInAsRole('ROLE_SUPER_ADMIN');
    }

    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn()
    {
        $this->getSession()->visit($this->generateUrl('_rednose_framework_security_logout'));
    }

    /**
     * Generate url.
     *
     * @param string  $route
     * @param array   $parameters
     * @param Boolean $absolute
     *
     * @return string
     */
    protected function generateUrl($route, array $parameters = array(), $absolute = false)
    {
        return $this->locatePath($this->get('router')->generate($route, $parameters, $absolute));
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

    /**
     * Get service by id.
     *
     * @param string $id
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * Presses button with specified id|name|title|alt|value.
     */
    protected function pressButton($button)
    {
        $this->getSession()->getPage()->pressButton($this->fixStepArgument($button));
    }

    /**
     * Clicks link with specified id|title|alt|text.
     */
    protected function clickLink($link)
    {
        $this->getSession()->getPage()->clickLink($this->fixStepArgument($link));
    }

    /**
     * Fills in form field with specified id|name|label|value.
     */
    protected function fillField($field, $value)
    {
        $this->getSession()->getPage()->fillField($this->fixStepArgument($field), $this->fixStepArgument($value));
    }

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }

    /**
     * Create user and login with given role.
     *
     * @param string $role
     * @param string $name
     */
    private function iAmLoggedInAsRole($role, $name = 'admin')
    {
//        $this->thereIsUser($email, 'rednose', $role);
        $this->getSession()->visit($this->generateUrl('_rednose_framework_security_login'));

        $this->fillField('username', $name);
        $this->fillField('password', 'adminpasswd');
        $this->pressButton('submit');
    }
}