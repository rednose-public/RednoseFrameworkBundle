<?php

namespace Rednose\FrameworkBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Rednose\FrameworkBundle\Model\UserInterface;

class FrameworkContext extends AbstractContext
{
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
        $util = $this->getContainer()->get('fos_user.util.user_manipulator');
        $em   = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var UserInterface $admin */
        $admin = $util->create('admin', 'adminpasswd', 'info@rednose.nl', true, true);
        $em->persist($admin);

        $this->login('admin', 'adminpasswd');
    }

    /**
     * @Given /^I am logged in as user$/
     */
    public function iAmLoggedInAsUser()
    {
        $util = $this->getContainer()->get('fos_user.util.user_manipulator');
        $em   = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var UserInterface $admin */
        $user = $util->create('user', 'userpasswd', 'info@rednose.nl', true, false);
        $em->persist($user);

        $this->login('user', 'userpasswd');
    }

    /**
     * @Given /^I am logged in as administrator for organization "([^"]*)"$/
     */
    public function iAmLoggedInAsAdministratorForOrganization($organization)
    {
        $this->createOrganization(array('name' => $organization));
        $this->iAmLoggedInAsAdministrator();
    }

    /**
     * @Given /^I am logged in as user for organization "([^"]*)"$/
     */
    public function iAmLoggedInAsUserForOrganization($organization)
    {
        $this->createOrganization(array('name' => $organization));
        $this->iAmLoggedInAsUser();
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
            $user = $util->create($data['name'], $data['password'], $data['email'], true, false);

            if (isset($data['organization'])) {
                $manager = $this->getContainer()->get('rednose_framework.organization_manager');
                $user->setOrganization($manager->findOrganizationBy(array('name' => $data['organization'])));
            }

            $um->updateUser($user);
        }
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