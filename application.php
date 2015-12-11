#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 11.12.15
 * Time: 20:06
 */


require __DIR__.'/vendor/autoload.php';

use AppBundle\Command\WeatherCheckCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new WeatherCheckCommand());
$application->run();