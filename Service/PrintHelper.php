<?php

namespace Inmarelibero\FirestarterBundle\Service;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrintHelper
{
    private $input;
    private $output;
    private $formatter;

    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @param $string
     * @param int $level 1|2|3 level of importance. 1 = more important
     */
    public function printHeader($string, $level = 1)
    {
        if ($level === 1) {
            $this->output->writeln(<<<EOD
<info>
############################################################################################
# {$string}
############################################################################################
</info>
EOD
            );
        }
    }

    /**
     * @param $string
     * @param $fatal
     */
    public function printSuccessMessage($string)
    {
        $this->output->writeln(<<<EOD
<info>-> {$string}</info>

EOD
    );
    }

    /**
     * @param $string
     * @param $fatal
     */
    public function printError($string, $fatal = false)
    {
        if ($fatal == true) {
            $formattedBlock = $this->formatter->formatBlock(array("", "FATAL ERROR!", $string, ""), 'error');

            $this->output->writeln($formattedBlock);

            return;
        }

        $this->output->writeln(<<<EOD
<fg=red>{$string}</fg=red>

EOD
        );
    }
} 