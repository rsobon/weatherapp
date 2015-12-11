<?php

namespace AppBundle\Integration;
use AppBundle\Entity\Weather;

/**
 * Created by PhpStorm.
 * User: r
 * Date: 11.12.15
 * Time: 22:55
 */
class YahooClient
{
    public function getWeather($location)
    {
        $BASE_URL = "http://query.yahooapis.com/v1/public/yql";
        $yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="' . $location . '") and u=\'c\'';
        $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json&u=c";
        // Make call with cURL
        $session = curl_init($yql_query_url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($session);
        // Convert JSON to PHP object
        $phpObj = json_decode($json);
        return $this->mapToEntity($phpObj->query->results);
    }

    private function mapToEntity($data)
    {
        $entity = new Weather();

        $entity->setLocation($data->channel->location->city);
        $entity->setTemperature($data->channel->item->condition->temp);
        $entity->setConditions($data->channel->item->condition->text);

        return $entity;

    }
}