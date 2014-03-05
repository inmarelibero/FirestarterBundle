<?php

namespace Inmarelibero\FirestarterBundle\Service;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;

class PrintHelper
{
    private $input,
        $output,
        $formatter
    ;

    public function setDependencies(InputInterface $input, OutputInterface $output, FormatterHelper $formatter)
    {
        $this->input = $input;
        $this->output = $output;
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
<fg=yellow>
############################################################################################
# {$string}
############################################################################################
</fg=yellow>
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