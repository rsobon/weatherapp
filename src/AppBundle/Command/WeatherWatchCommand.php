<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 11.12.15
 * Time: 21:19
 */

namespace AppBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeatherWatchCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    protected function configure()
    {
        $this
            ->setName('weather:watch')
            ->setDescription('Watch the weather in location')
            ->addArgument('location', InputArgument::OPTIONAL, 'Which location would you like to check?')
            ->addOption('period', null, InputOption::VALUE_REQUIRED, 'Set the polling period in seconds', 2);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $yahoo = $this->getContainer()->get('app.integration.yahoo_client');
        $event = $this->getContainer()->get('app.event.weather_listener');

        $location = $input->getArgument('location') ?: "London";

        // capture error output
        $stderr = $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : $output;
        $error = '';

        while (true) {
            try {
                $weather = $yahoo->getWeather($location);
                $output->writeln('Weather for: ' . $weather->getLocation());
                $output->writeln('Temp: ' . $weather->getTemperature());
                $output->writeln('Conditions: ' . $weather->getConditions());

                // get the last weather for this location from database
                if(!$event->getLastWeather()){
                    $lastWeather = $this->em->getRepository('AppBundle:Weather')->findOneBy(
                        array('location' => $weather->getLocation()),
                        array('id' => 'DESC')
                    );
                    $event->setLastWeather($lastWeather);
                }

                // if there is a weather update save to database
                if($event->compareWeather($weather)) {
                    $this->em->persist($weather);
                    $this->em->flush();
                };

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