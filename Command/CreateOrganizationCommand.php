<?php

namespace Rednose\FrameworkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateOrganizationCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('rednose:framework:create-organization')
            ->setDescription('Create an organization.')
            ->setDefinition(array(
                new InputArgument('name', InputArgument::REQUIRED, 'The organization name'),
            ))
            ->setHelp(<<<EOT
The <info>rednose:framework:create-organization</info> command creates an organization with default settings:

  <info>php app/console rednose:framework:create-organization Rijksgebouwendienst</info>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $manager = $this->getContainer()->get('rednose_framework.organization_manager');

        $organization = $manager->createOrganization($name);
        $manager->updateOrganization($organization);

        $output->writeln(sprintf('Created organization <comment>%s</comment>', $name));
    }
}
