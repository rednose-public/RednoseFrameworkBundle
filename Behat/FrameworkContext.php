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
        $user = $util->create('user', 'userpasswd', 'info@rednose.nl', true, false);
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
            $user = $util->create($data['name'], $data['password'], $data['email'], true, false);

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