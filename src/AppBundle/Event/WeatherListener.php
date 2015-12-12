<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 12.12.15
 * Time: 09:34
 */

namespace AppBundle\Event;


use AppBundle\Entity\Weather;
use AppBundle\Integration\YahooClient;
use AppBundle\Message\MessageSender;
use Doctrine\ORM\EntityManager;

class WeatherListener
{

    /**
     * If true there will be no database queries performed
     * @var bool
     */
    private $noDatabase;

    /**
     * @var Weather
     */
    private $currentWeather;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MessageSender
     */
    private $messageSender;

    /**
     * @var YahooClient
     */
    private $yahooClient;

    /**
     * WeatherListener constructor.
     * @param EntityManager $entityManager
     * @param MessageSender $messageSender
     * @param YahooClient $yahooClient
     * @internal param Weather $lastWeather
     */
    public function __construct(EntityManager $entityManager, MessageSender $messageSender, YahooClient $yahooClient)
    {
        $this->entityManager = $entityManager;
        $this->messageSender = $messageSender;
        $this->yahooClient = $yahooClient;
    }

    /**
     * Function that configures the Listener Service
     * @param bool $noDatabase
     */
    public function configure($noDatabase = false)
    {
        $this->setNoDatabase($noDatabase);
    }

    /**
     * Watch for changes of either temperature or conditions
     * Returns false if there is no weather update
     * @param $location
     * @return bool
     */
    public function watchWeather($location)
    {
        $fetchedWeather = $this->yahooClient->getWeather($location);

        $message_array = array();
        $message_array['conditions'] = $this->compareConditions($fetchedWeather, $this->currentWeather);
        $message_array['temp'] = $this->compareTemp($fetchedWeather, $this->currentWeather);

        // check if either conditions or temperature changed
        if (in_array(true, $message_array)) {
            // send messages
            $this->messageSender->sendMessage($message_array);
            // update current weather and save
            $this->setCurrentWeather($fetchedWeather);
            if(!$this->getNoDatabase()) {
                $this->entityManager->persist($fetchedWeather);
                $this->entityManager->flush();
            }
            return true;

        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getNoDatabase()
    {
        return $this->noDatabase;
    }

    /**
     * @param mixed $noDatabase
     */
    public function setNoDatabase($noDatabase)
    {
        $this->noDatabase = $noDatabase;
    }

    /**
     * @return mixed
     */
    public function getCurrentWeather()
    {
        return $this->currentWeather;
    }

    /**
     * @param mixed $newWeather
     */
    public function setCurrentWeather($newWeather)
    {
        $this->currentWeather = $newWeather;
    }

    /**
     * Returns a message if conditions changed
     * Returns false if there is no weather update
     * @param Weather $fetchedWeather
     * @param Weather $lastWeather
     * @return mixed
     */
    private function compareConditions($fetchedWeather, $lastWeather)
    {
        if (!isset($lastWeather)) {
            return sprintf("Conditions in %s are %s", $fetchedWeather->getLocation(), $fetchedWeather->getConditions());
        } else if (isset($lastWeather) && ($fetchedWeather->getConditions() != $lastWeather->getConditions())) {
            return sprintf("Conditions in %s changed from %s to %s", $fetchedWeather->getLocation(), $lastWeather->getConditions(), $fetchedWeather->getConditions());
        }
        return false;
    }

    /**
     * Returns a message if temperature changed
     * Returns false if there is no weather update
     * @param Weather $fetchedWeather
     * @param Weather $lastWeather
     * @return mixed
     */
    private function compareTemp($fetchedWeather, $lastWeather)
    {
        if (!isset($lastWeather)) {
            return sprintf("Temperature in %s is %s", $fetchedWeather->getLocation(), $fetchedWeather->getTemperature());
        } else if (isset($lastWeather) && ($fetchedWeather->getTemperature() != $lastWeather->getTemperature())) {
            return sprintf("Temperature in %s changed from %s to %s", $fetchedWeather->getLocation(), $lastWeather->getTemperature(), $fetchedWeather->getTemperature());
        }
        return false;
    }

    /**
     * Queries database for last existing weather entry and returns console output message
     * @param $location
     * @return array
     */
    public function findCurrentWeather($location)
    {
        // just the output for console
        $output_array = array();

        // get the full location name from Yahoo API
        $fullLocation = $this->yahooClient->getLocation($location);

        // get the last weather for this location from database
        if(!isset($this->currentWeather)){

            $output_array[] = "Quering database for weather entries for: " . $fullLocation;
            $lastWeather = $this->entityManager->getRepository('AppBundle:Weather')->findOneBy(
                array('location' => $fullLocation),
                array('id' => 'DESC')
            );

            if(isset($lastWeather)) {
                $this->setCurrentWeather($lastWeather);
                $output_array[] = sprintf("Last weather entry in database for %s:", $fullLocation);
                $output_array[] = sprintf("Conditions in %s are %s", $this->currentWeather->getLocation(), $this->currentWeather->getConditions());
                $output_array[] = sprintf("Temperature in %s is %s", $this->currentWeather->getLocation(), $this->currentWeather->getTemperature());
            } else {
                $output_array[] = "Not found any weather entries in database for: " . $fullLocation;
            }
        }
        return $output_array;
    }




}