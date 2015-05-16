<?php

include_once '../ccny/scidiv/cores/autoloader.php';
include_once '../ccny/scidiv/cores/config/config.php';
include_once '../ccny/scidiv/cores/view/CoreView.php';

use ccny\scidiv\cores\view\CoreView as CoreView;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Session\Session as Session;

$request = Request::createFromGlobals();

$session = new Session();
$session->start();

$err_msg = $session->get('err_msg',null);
$session->remove('err_msg');

$val_code = $request->query->get('id', null);

$view = new CoreView();
$view->loadTemplate('recover_newpassword.html.twig');

$template_vars = ["err_msg"=>$err_msg,"val_code"=>$val_code];

echo $view->render($template_vars);