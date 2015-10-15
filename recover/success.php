<?php

include_once __DIR__ . '/../vendor/autoload.php';

use ccny\scidiv\cores\view\CoreView as CoreView;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Session\Session as Session;

$request = Request::createFromGlobals();
$session = new Session();
$session->start();

$info_msg = $session->get('info_msg',null);
$session->remove('info_msg');

$view = new CoreView(__DIR__ . '/resources/templates');
$view->loadTemplate('recover_success.html.twig');

$template_vars = array("info_msg"=>$info_msg);

echo $view->render($template_vars);
