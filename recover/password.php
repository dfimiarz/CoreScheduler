<?php

include_once '../ccny/scidiv/cores/autoloader.php';
include_once '../ccny/scidiv/cores/components/Utils.php';
include_once '../ccny/scidiv/cores/config/config.php';
include_once '../ccny/scidiv/cores/view/CoreView.php';

use ccny\scidiv\cores\view\CoreView as CoreView;
use Symfony\Component\HttpFoundation\Request as Request;

$request = Request::createFromGlobals();

$err_msg = $request->request->get('err_msg',null);

$view = new CoreView();
$view->loadTemplate('recover_password.html.twig');

$template_vars = ["err_msg"=>$err_msg];

echo $view->render($template_vars);

