<?php

    require_once 'vendor/autoload.php';

    $config = \Aztech\Phinject\Config\ConfigFactory::fromFile(__DIR__ . '/01-simple-config.json');
    $container = \Aztech\Phinject\ContainerFactory::create($config);

    $obj = $container->get('SimpleObject');

    echo $obj->property . PHP_EOL;
