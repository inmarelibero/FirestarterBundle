<?php

namespace Inmarelibero\FirestarterBundle\Service;

use Symfony\Component\Process\Process;

class CommandHelper
{
    private $kernelRootDir;

    public function __construct($kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * Execute a command writing on the $output the result in real time
     *
     * @param $command
     * @return Process
     */
    public function executeCommand($command)
    {
        chdir($this->kernelRootDir.'/..');

        $process = new Process($command);

        $process->setTimeout(360);

        $process->run(function ($type, $buffer) use (&$process) {
            if (Process::ERR === $type) {
                echo $buffer;
                $process->stop(3, SIGINT);
            } else {
                echo $buffer;
            }
        });

        return $process;
    }
} 