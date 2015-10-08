<?php

namespace Rednose\FrameworkBundle\Command;

use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateUuidCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rednose:framework:generate-uuid')
            ->setDescription('Generates a UUID')
            ->setHelp(<<<EOT
The <info>rednose:framework:generate-uuid</info> command generates a UUID:

<info>php app/console rednose:framework:generate-uuid</info>
EOT
            );
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $generator = new UuidGenerator();

        $output->writeln('');
        $output->writeln(sprintf('<info>%s</info>', $generator->generate($em, null)));
        $output->writeln('');
    }
}
