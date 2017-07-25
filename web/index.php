<?php

include __DIR__ . "/../vendor/autoload.php";

$app = new Silex\Application();

$app["debug"] = true;

$app->register(new \Silex\Provider\TwigServiceProvider(),array(
    'twig.path' => __DIR__ . '/resources/corescheduler/views',
));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\AssetServiceProvider(), array());

$app->register(new Silex\Provider\RoutingServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'dbname'     => 'scidiv',
        'host'  => 'localhost',
        'user' => 'root',
        'password' => ''
    )
));

$app->get("/", 'ccny\\scidiv\\cores\\ctrl\\HomeController::indexAction')->bind('home');

$app->get("/login", 'ccny\\scidiv\\cores\\ctrl\\UserController::loginAction')->bind('login');

$app->post("/login", 'ccny\\scidiv\\cores\\ctrl\\UserController::doLoginAction')->bind('dologin');

$app->mount("manage", include_once __DIR__ . '/../ccny/scidiv/cores/app/routes/manage.php')->before(
    function(\Symfony\Component\HttpFoundation\Request $request, Silex\Application $app ){
        $request->attributes->set("is_admin", false);
    }
);

$app->run();