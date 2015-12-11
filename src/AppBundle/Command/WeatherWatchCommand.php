<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 11.12.15
 * Time: 21:19
 */

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeatherWatchCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('weather:watch')
            ->setDescription('Watch the weather in location')
            ->addArgument('location', InputArgument::OPTIONAL, 'Which location would you like to check?')
            ->addOption('period', null, InputOption::VALUE_REQUIRED, 'Set the polling period in seconds', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // capture error output
        $stderr = $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : $output;

        $error = '';
        while (true) {
            try {

                $name = $input->getArgument('location');
                if ($name) {
                    $text = 'Hello ' . $name;
                } else {
                    $text = 'Hello';
                }

                $output->writeln($text);

                $error = '';
            } catch (\Exception $e) {
                if ($error != $msg = $e->getMessage()) {
                    $stderr->writeln('<error>[error]</error> ' . $msg);
                    $error = $msg;
                }
            }

            clearstatcache();
            sleep($input->getOption('period'));
        }
    }


}