<?php

include_once __DIR__ . '/autoloader.php';
include_once '../ccny/scidiv/cores/config/config.php';
include_once '../ccny/scidiv/cores/view/CoreView.php';

use ccny\scidiv\cores\view\CoreView as CoreView;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Session\Session as Session;

$request = Request::createFromGlobals();
$session = new Session();
$session->start();

$user_id = $request->query->get('uid',null);

$view = new CoreView(__DIR__ . '/view/templates');
$view->loadTemplate('user_profile.html.twig');

$template_vars = [];

echo $view->render($template_vars);