<?php

include __DIR__ . "/../vendor/autoload.php";

$app = new Silex\Application();

$app["debug"] = true;

$app->register(new \Silex\Provider\TwigServiceProvider(),array(
    'twig.path' => __DIR__ . '/resources/corescheduler/views',
));

$app->register(new Silex\Provider\AssetServiceProvider(), array());

$app->get("/", 'ccny\scidiv\cores\ctrl\HomePageController::indexAction')->bind('homepage');

$app->run();