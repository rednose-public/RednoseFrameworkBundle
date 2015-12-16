<?php

namespace Rednose\FrameworkBundle\Behat;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Rednose\FrameworkBundle\Entity\Group;
use Rednose\FrameworkBundle\Model\UserInterface;

class FrameworkContext extends AbstractContext implements SnippetAcceptingContext
{
    /**
     * @Then /^I pause$/
     */
    public function iPause()
    {
        $this->getSession()->wait(3600000);
    }

    /**
     * First, force logout, then go to the login page, fill the informations and finally go to requested page
     *
     * @Given /^I am connected with "([^"]*)" and "([^"]*)" on "([^"]*)"$/
     *
     * @param string $login
     * @param string $rawPassword
     * @param string $url
     */
    public function iAmConnectedWithOn($login, $rawPassword, $url)
    {
        $this->getSession()->visit($this->locatePath($url));

        $this->fillField('username', $login);
        $this->fillField('password', $rawPassword);
        $this->pressButton('submit');
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