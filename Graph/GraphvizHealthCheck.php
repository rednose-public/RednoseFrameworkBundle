<?php

namespace Rednose\FrameworkBundle\Graph;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Result\CheckResult;
use Symfony\Component\Process\ProcessBuilder;
use Liip\Monitor\Exception\CheckFailedException;

class GraphvizHealthCheck extends Check
{
    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $arguments = array(
            'dot',
            // Dummy filename.
            'test.test',
        );

        $builder = new ProcessBuilder($arguments);
        $process = $builder->getProcess();

        $process->run();

        // Process should always fail, because the dummy file shouldn't exist.
        if ($process->isSuccessful()) {
            throw new CheckFailedException();
        }

        if (strpos($process->getErrorOutput(), 'test.test') !== false) {
            // Dot explicitly mentions the filename, so it's available and functioning to a basic extent.
            return $this->buildResult('OK', CheckResult::OK);
        }

        return $this->buildResult(sprintf('KO - %s', trim($process->getErrorOutput())), CheckResult::CRITICAL);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Graphviz Health Check';
    }
}
