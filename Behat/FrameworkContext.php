<?php

namespace Rednose\FrameworkBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Rednose\FrameworkBundle\Model\UserInterface;

class FrameworkContext extends AbstractContext
{
    /**
     * @Then /^I pause$/
     */
    public function iPause()
    {
        $this->getSession()->wait(3600000);
    }

    /**
     * @Given /^I am logged in as administrator$/
     */
    public function iAmLoggedInAsAdministrator($organization)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $admin = $this->getContainer()->get('rednose_framework.user_manager')->findUserByUsername('admin');
        $admin->setOrganization($organization);

        $em->persist($admin);
        $em->flush();

        $this->login('admin', 'adminpasswd');
    }

    /**
     * @Given /^I am logged in as user$/
     */
    public function iAmLoggedInAsUser($organization = null)
    {
        $util = $this->getContainer()->get('fos_user.util.user_manipulator');
        $em   = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var UserInterface $admin */
        $user = $util->create('user', 'userpasswd', 'user@rednose.nl', true, false);
        $user->setOrganization($organization);

        $em->persist($user);
        $em->flush();

        $this->login('user', 'userpasswd');
    }

    /**
     * @Given /^I am logged in as administrator for organization "([^"]*)"$/
     */
    public function iAmLoggedInAsAdministratorForOrganization($organization)
    {
        $organization = $this->getOrganization($organization);
        $this->iAmLoggedInAsAdministrator($organization);
    }

    /**
     * @Given /^I am logged in as user for organization "([^"]*)"$/
     */
    public function iAmLoggedInAsUserForOrganization($organization)
    {
        $organization = $this->getOrganization($organization);
        $this->iAmLoggedInAsUser($organization);
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
     */
    public function thereAreUsers(TableNode $table)
    {
        $util = $this->getContainer()->get('fos_user.util.user_manipulator');
        $um   = $this->getContainer()->get('rednose_framework.user_manager');

        foreach ($table->getHash() as $data) {
            /** @var UserInterface $admin */
            $user = $util->create($data['name'], $data['password'], $data['email'], true, isset($data['admin']));

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
     * @Given /^I log in as admin for organization "([^"]*)"$/
     */
    public function imLogInAsAdminForOrganization($organization)
    {
        $organization = $this->getOrganization($organization);

        $util = $this->getContainer()->get('fos_user.util.user_manipulator');
        $em   = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var UserInterface $admin */
        $user = $util->create('testadmin', 'testadmin', 'testadmin@rednose.nl', true, true);
        $user->setOrganization($organization);

        $em->persist($user);
        $em->flush();

        $this->fillField('username', 'testadmin');
        $this->fillField('password', 'testadmin');
        $this->pressButton('submit');
    }

    /**
     * @Given /^I log in as user for organization "([^"]*)"$/
     */
    public function imLogInAsUserForOrganization($organization)
    {
        $organization = $this->getOrganization($organization);

        $util = $this->getContainer()->get('fos_user.util.user_manipulator');
        $em   = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var UserInterface $admin */
        $user = $util->create('user', 'userpasswd', 'user@rednose.nl', true, false);
        $user->setOrganization($organization);

        $em->persist($user);
        $em->flush();

        $this->fillField('username', 'user');
        $this->fillField('password', 'userpasswd');
        $this->pressButton('submit');
    }

    // -- Page interactions ----------------------------------------------------

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

    /**
     * @param string $name
     * @param string $password
     */
    private function login($name, $password)
    {
        $this->getSession()->visit($this->generateUrl('_rednose_framework_security_login'));

        $this->fillField('username', $name);
        $this->fillField('password', $password);
        $this->pressButton('submit');
    }
}