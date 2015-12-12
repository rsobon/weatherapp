<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 11.12.15
 * Time: 21:19
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeatherWatchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('weather:watch')
            ->setDescription('Watch the weather in location')
            ->addArgument('location', InputArgument::OPTIONAL, 'Which location would you like to check?')
            ->addOption('period', null, InputOption::VALUE_REQUIRED, 'Set the polling period in seconds', 600)
            ->addOption('no-database', null, InputOption::VALUE_NONE, 'Use if you do not wish to use database');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $event = $this->getContainer()->get('app.event.weather_listener');
        $location = $input->getArgument('location') ?: "London";
        if(!$input->getOption('no-database')) {
            $output_message = $event->findCurrentWeather($location);
            $this->printConsoleOutput($output_message, $output);
        }

        // capture error output
        $stderr = $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : $output;
        $error = '';

        while (true) {
            try {
                $this->printConsoleOutput('Quering Yahoo API for weather update.', $output);
                $event->watchWeather($location, $input->getOption('no-database'));
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

    /**
     * @param $output_message
     */
    private function printConsoleOutput($output_message, OutputInterface $output) {
        if(is_array($output_message)) {
            foreach ($output_message as $value) {
                if ($value) {
                    $output->writeln($value);
                }
            }
        } else {
            $output->writeln($output_message);
        }
    }
}