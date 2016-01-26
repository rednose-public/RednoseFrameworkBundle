<?php

namespace Rednose\FrameworkBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Rednose\FrameworkBundle\Entity\Group;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class FrameworkContext extends AbstractContext
{
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
            $this->getSession()->visit($this->generateUrl('_rednose_framework_security_login'));
        }

        // Set the cookie
        $minkSession = $this->getSession();
        $minkSession->setCookie($session->getName(), $session->getId());
    }

    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn()
    {
        $this->getSession()->visit($this->generateUrl('_rednose_framework_security_logout'));
    }

    /**
     * @param TableNode $table
     *
     * @Given /^the following organizations are defined:$/
     */
    public function thereAreOrganizations(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->createOrganization($data);
        }
    }

    /**
     * @param TableNode $table
     *
     * @Given /^the following users are defined:$/
     * @Given /^there are users:$/
     */
    public function thereAreUsers(TableNode $table)
    {
        $util = $this->getContainer()->get('fos_user.util.user_manipulator');
        $um   = $this->getContainer()->get('rednose_framework.user_manager');

        foreach ($table->getHash() as $data) {
            /** @var UserInterface $admin */
            $user = $util->create($data['username'], $data['password'], $data['email'], true, isset($data['admin']));

            if (isset($data['roles'])) {
                $roles = explode(',', $data['roles']);

                foreach ($roles as $role) {
                    $user->addRole(trim($role));
                }
            }

            if (isset($data['groups'])) {
                $groups = explode(',', $data['groups']);

                foreach ($groups as $group) {
                    $group = trim($group);

                    if (!$group) {
                        continue;
                    }

                    $model = $this->get('rednose_framework.group_manager')->findGroupByName($group);

                    if (!$model) {
                        $model = new Group($group);
                        $model->setOrganization($this->get('rednose_framework.organization_manager')->findOrganizationBy(['name' => 'Test']));
                        $this->get('rednose_framework.group_manager')->updateGroup($model);
                    }

                    $user->addGroup($model);
                }
            }

            if (isset($data['organization'])) {
                $manager = $this->getContainer()->get('rednose_framework.organization_manager');
                $organization = $manager->findOrganizationBy(array('name' => $data['organization']));

                if (!$organization) {
                    $organization = $manager->createOrganization();
                    $organization->setName($data['organization']);
                    $manager->updateOrganization($organization);
                }


                $user->setOrganization($organization);
            }

            $um->updateUser($user);
        }
    }

    /**
     * @Then :arg1 should have role :arg2
     */
    public function shouldHaveRole($arg1, $arg2)
    {
        $user = $this->get('rednose_framework.user_manager')->findUserByUsername($arg1);

        if (!$user) {
            throw new ExpectationException(sprintf('User "%s" not found', $arg1), $this->getSession());
        }

        if (!$user->hasRole($arg2)) {
            throw new ExpectationException(sprintf('Expected user "%s" to have role "%s"', $arg1, $arg2), $this->getSession());
        }
    }

    /**
     * @Then :arg1 should not have role :arg2
     */
    public function shouldNotHaveRole($arg1, $arg2)
    {
        $user = $this->get('rednose_framework.user_manager')->findUserByUsername($arg1);

        if (!$user) {
            throw new ExpectationException(sprintf('User "%s" not found', $arg1), $this->getSession());
        }

        if ($user->hasRole($arg2)) {
            throw new ExpectationException(sprintf('Expected user "%s" not to have role "%s"', $arg1, $arg2), $this->getSession());
        }
    }

    // -- Page interactions ----------------------------------------------------

    /**
     * @Then control :arg1 should have a label
     */
    public function controlShouldHaveALabel($arg1)
    {
        $field = $this->getSession()->getPage()->find('css', '[ng-model=\'' . $arg1 . '\']');

        if (null === $field) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), 'form field', 'ng-model', $arg1);
        }

        $label = $this->getSession()->getPage()->find('css', 'label[for=\'' . $field->getAttribute('id') . '\']');

        if (null === $label) {
            throw new ExpectationException(sprintf('Control "%s" was expected to have a label', $arg1), $this->getSession());
        }
    }

    /**
     * @Then control :arg1 should not have a label
     */
    public function controlShouldNotHaveALabel($arg1)
    {
        $field = $this->getSession()->getPage()->find('css', '[ng-model=\'' . $arg1 . '\']');

        if (null === $field) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), 'form field', 'ng-model', $arg1);
        }

        $label = $this->getSession()->getPage()->find('css', 'label[for=\'' . $field->getAttribute('id') . '\']');

        if (null !== $label) {
            throw new ExpectationException(sprintf('Control "%s" was expected to have no label', $arg1), $this->getSession());
        }
    }

    /**
     * @Then I should see an option :arg1 in :arg2
     */
    public function iShouldSeeAnOptionIn($arg1, $arg2)
    {
        $this->waitForAngular();

        $arg1 = $this->fixStepArgument($arg1);
        $arg2 = $this->fixStepArgument($arg2);

        $field = $this->getSession()->getPage()->findField($arg2);

        if (null === $field) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), 'form field', 'id|name|label|value', $arg2);
        }

        if ($field->getAttribute('rn-autocomplete') === null) {
            $option =  $field->find('css', 'option:contains(\'' . $arg1 . '\')');
        } else {
            $option =  $field->getParent()->find('css', '.tt-suggestion:contains(\'' . $arg1 . '\')');
        }

        if (null === $option) {
            throw new ExpectationException(sprintf('Option "%s" was expected in form field "%s"', $arg1, $arg2), $this->getSession());
        }
    }

    /**
     * @Then I should not see an option :arg1 in :arg2
     */
    public function iShouldNotSeeAnOptionIn($arg1, $arg2)
    {
        $this->waitForAngular();

        $arg1 = $this->fixStepArgument($arg1);
        $arg2 = $this->fixStepArgument($arg2);

        $field = $this->getSession()->getPage()->findField($arg2);

        if ($field->getAttribute('rn-autocomplete') === null) {
            $option =  $field->find('css', 'option:contains(\'' . $arg1 . '\')');
        } else {
            $option =  $field->getParent()->find('css', '.tt-suggestion:contains(\'' . $arg1 . '\')');
        }

        if (null !== $option) {
            throw new ExpectationException(sprintf('Option "%s" was not expected in form field "%s"', $arg1, $arg2), $this->getSession());
        }
    }

    /**
     * @Then I select the option :arg1 in :arg2
     */
    public function iShouldSelectOptionInField($arg1, $arg2)
    {
        $this->waitForAngular();

        $arg1 = $this->fixStepArgument($arg1);
        $arg2 = $this->fixStepArgument($arg2);

        $field = $this->getSession()->getPage()->findField($arg2);

        // Option value
        if ($option = $field->find('css', 'option[value=\'' . $arg1 . '\']')) {
            $field->selectOption($option->getAttribute('value'));

            return;
        }

        // Option innerHTML
        if ($option = $field->find('css', 'option:contains(\'' . $arg1 . '\')')) {
            $field->selectOption($option->getAttribute('value'));

            return;
        }

        throw new ExpectationException(sprintf('Field "%s" was not found in form ', $arg2), $this->getSession());
    }

    /**
     * @Then I should see an selected option :arg1 in :arg2
     */
    public function iShouldSeeSelectedOptionInField($arg1, $arg2)
    {
        $this->waitForAngular();

        $arg1 = $this->fixStepArgument($arg1);
        $arg2 = $this->fixStepArgument($arg2);

        $field = $this->getSession()->getPage()->findField($arg2);

        if ($arg1 === $field->getValue()) {
            return;
        }

        // Option innerHTML
        if ($option = $field->find('css', 'option[selected]')) {
            if ($option->getHtml() === $arg1) {
                return;
            }

        }

        throw new ExpectationException(sprintf('Option "%s" was not found in form field "%s"', $arg1, $arg2), $this->getSession());
    }

    /**
     * @Then /^(?:|I )should see (?<at>|at least )"(?P<count>(?:[^"]|\\")*)" options in "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function iShouldSeeOptionsInField($atLeast, $count, $locator)
    {
        $this->waitForAngular();

        $count = $this->fixStepArgument($count);
        $locator = $this->fixStepArgument($locator);

        $field = $this->getSession()->getPage()->findField($locator);

        if (null === $field) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), 'form field', 'id|name|label|value', $locator);
        }

        if ($field->getAttribute('rn-autocomplete') === null) {
            $optionCount = count($field->findAll('css', 'option'));
        } else {
            $optionCount = count($field->getParent()->findAll('css', '.tt-suggestion'));
        }

        if (!!$atLeast ? ($optionCount < (int) $count) : ($optionCount !== (int) $count)) {
            throw new ExpectationException(sprintf('Expected %d options for field %s, but found %d options instead', $count, $locator, $optionCount), $this->getSession()->getDriver());
        }
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

    protected function getOrganization($name)
    {
        if (!$name) {
            return null;
        }

        $manager = $this->getContainer()->get('rednose_framework.organization_manager');

        $organization = $manager->findOrganizationBy(array('name' => $name));

        if (!$organization) {
            $organization = $manager->createOrganization();
            $organization->setName($name);
            $manager->updateOrganization($organization);
        }

        return $organization;
    }

    protected function createOrganization($data)
    {
        $manager = $this->getContainer()->get('rednose_framework.organization_manager');

        $organization = $manager->createOrganization();
        $organization->setName($data['name']);
        $manager->updateOrganization($organization);
    }
}