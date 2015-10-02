<?php

include_once './vendor/autoload.php';

use ccny\scidiv\cores\view\CoreView as CoreView;
use ccny\scidiv\cores\components\Utils as Utils;
use ccny\scidiv\cores\config\Config as Config;

$utils = Utils::getObject();

$rid = $utils->getRID();

$view = new CoreView(__DIR__ . '/ccny/scidiv/cores/view/templates/');
$view->loadTemplate('main.html.twig');

$arr_variables = ["rid" => $rid,"page_title"=>Config::APP_NAME,"icon"=>Config::APP_ICON];

echo $view->render($arr_variables);



