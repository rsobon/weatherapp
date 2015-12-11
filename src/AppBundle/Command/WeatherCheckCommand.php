<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 11.12.15
 * Time: 19:59
 */

namespace AppBundle\Command;

use AppBundle\Entity\Weather;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Integration\YahooClient;

class WeatherCheckCommand extends ContainerAwareCommand
{
    private $em;

    protected function configure()
    {
        $this
            ->setName('weather:check')
            ->setDescription('Check the weather in location')
            ->addArgument(
                'location',
                InputArgument::OPTIONAL,
                'Which location would you like to check?'
            )
            ->addOption('save', null, InputOption::VALUE_NONE, 'Persist to database');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $location = $input->getArgument('location') ?: "London";

        $yahoo = new YahooClient();
        $weather = $yahoo->getWeather($location);
        $output->writeln('weather for: ' . $weather->getLocation());
        $output->writeln('temp: ' . $weather->getTemperature());
        $output->writeln('conditions: ' . $weather->getConditions());

        if ($input->getOption('save')) {
            $this->em->persist($weather);
            $this->em->flush();
        }
    }
}