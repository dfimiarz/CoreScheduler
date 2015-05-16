<?php

include_once '../ccny/scidiv/cores/autoloader.php';
include_once '../ccny/scidiv/cores/components/Utils.php';
include_once '../ccny/scidiv/cores/config/config.php';
include_once '../ccny/scidiv/cores/view/CoreView.php';

use ccny\scidiv\cores\view\CoreView as CoreView;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Session\Session as Session;

$request = Request::createFromGlobals();
$session = new Session();
$session->start();

$info_msg = $session->get('info_msg',null);
$session->remove('info_msg');

$view = new CoreView();
$view->loadTemplate('recover_success.html.twig');

$template_vars = ["info_msg"=>$info_msg];

echo $view->render($template_vars);
