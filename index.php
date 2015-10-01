<?php

include_once './vendor/autoload.php';
include_once './ccny/scidiv/cores/config/config.php';

use ccny\scidiv\cores\view\CoreView as CoreView;
use ccny\scidiv\cores\components\Utils as Utils;

$utils = Utils::getObject();

$rid = $utils->getRID();

$view = new CoreView(__DIR__ . '/ccny/scidiv/cores/view/templates/');
$view->loadTemplate('main.html.twig');

$arr_variables = ["rid" => $rid,"page_title"=>"DivOfScience - Conference Room Reservations","icon"=>SYSTEM_ICON];

echo $view->render($arr_variables);



