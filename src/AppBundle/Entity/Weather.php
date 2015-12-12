<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Traits\TimestampableTrait;

/**
 * Weather
 *
 * Created by PhpStorm.
 * User: r
 * Date: 11.12.15
 * Time: 21:26
 *
 * @ORM\Table()
 * @ORM\Entity()
 */

class Weather
{
	use TimestampableTrait;

	/**
	 * @var integer
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
     * A city
	 * @var string
	 * @ORM\Column(name="location", type="string", length=255)
	 */
	private $location;

	/**
     * Temperature in celcius
	 * @var integer
	 * @ORM\Column(name="temperature", type="integer")
	 */
	private $temperature;


	/**
     * Weather conditions description
	 * @var string
	 * @ORM\Column(name="conditions", type="text")
	 */
	private $conditions;


    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set location
     * @param string $location
     * @return Weather
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set temperature
     * @param integer $temperature
     * @return Weather
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Get temperature
     * @return integer
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * Set conditions
     * @param string $conditions
     * @return Weather
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * Get conditions
     * @return string
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}
