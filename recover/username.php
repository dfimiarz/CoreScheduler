<?php

include_once __DIR__ . '/../vendor/autoload.php';


use ccny\scidiv\cores\view\CoreView as CoreView;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Session\Session as Session;



$request = Request::createFromGlobals();
$session = new Session();
$session->start();

$err_msg = $session->get('err_msg',null);
$session->remove('err_msg');

$view = new CoreView(__DIR__ . '/view/templates');
$view->loadTemplate('recover_username.html.twig');

$template_vars = array("err_msg"=>$err_msg);

echo $view->render($template_vars);
