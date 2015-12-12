<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 12.12.15
 * Time: 09:34
 */

namespace AppBundle\Event;


use AppBundle\Entity\Weather;

class WeatherListener
{
    /**
     * @var Weather
     */
    private $lastWeather;

    /**
     * Returns true and sends a message if either temperature or conditions changed
     * Returns false if there is no weather update
     * @param Weather $fetchedWeather
     * @return bool
     */
    public function compareWeather($fetchedWeather)
    {
        $message_array = array();
        $message_array['conditions'] = $this->compareConditions($fetchedWeather, $this->lastWeather);
        $message_array['temp'] = $this->compareTemp($fetchedWeather, $this->lastWeather);

        // check if either conditions or temperature changed
        if (in_array(true, $message_array)) {
            foreach ($message_array as $key => $value) {
                if ($value) {
                    $this->setLastWeather($fetchedWeather);
                    $this->sendMessage($value);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getLastWeather()
    {
        return $this->lastWeather;
    }

    /**
     * @param mixed $newWeather
     */
    public function setLastWeather($newWeather)
    {
        $this->lastWeather = $newWeather;
    }

    /**
     * @param $message
     */
    private function sendMessage($message)
    {
        printf($message . "\n");
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
            return 'Conditions in ' .
            $fetchedWeather->getLocation() .
            ' are ' .
            $fetchedWeather->getConditions();
        } else if (isset($lastWeather) && ($fetchedWeather->getConditions() != $lastWeather->getConditions())) {
            return 'Conditions in ' .
            $fetchedWeather->getLocation() .
            ' changed from ' .
            $lastWeather->getConditions() .
            ' to ' .
            $fetchedWeather->getConditions();
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
            return 'Temperature in ' .
            $fetchedWeather->getLocation() .
            ' is ' .
            $fetchedWeather->getTemperature();
        } else if (isset($lastWeather) && ($fetchedWeather->getTemperature() != $lastWeather->getTemperature())) {
            return 'Temperature in ' .
            $fetchedWeather->getLocation() .
            ' changed from ' .
            $lastWeather->getTemperature() .
            ' to ' .
            $fetchedWeather->getTemperature();
        }
        return false;

    }

}