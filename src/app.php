<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Yaml\Yaml;

$app = new Application();


$app['debug'] = true;
$app['config'] = Yaml::parse(file_get_contents(__DIR__ . '/../config/' . 'settings.yml'));

return $app;