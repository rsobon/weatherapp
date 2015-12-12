Symfony 2 WeatherApp
=========

This is a small Symfony 2 console application that is requesting Yahoo Weather API (https://developer.yahoo.com/weather/) for weather updates.

Installation
------------

Clone project and run composer:
```bash
$ git clone git@github.com:rsobon/weatherapp.git
$ cd weatherapp/
$ composer install
```

Adjust parameter.yml accordingly to your database configuration and then:
```bash
$ app/console doctrine:database:create
$ app/console doctrine:schema:update
```

Usage
------------

Run weather:check command in order to display weather condition (enter any city or location as argument):
```bash
$ app/console weather:check Warsaw
```

Add --save option to persist weather into database:
```bash
$ app/console weather:check London --save
```

Run weather:watch command in order to periodically query Yahoo API for weather updates. Application will save to database only if there exists a new weather condition.
```bash
$ app/console weather:watch Warsaw
```

Add --period option to set how often (in seconds) should application check for weather updates (default is 600 seconds):
```bash
$ app/console weather:check London --period=10
```