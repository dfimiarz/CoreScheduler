<?php

include_once './ccny/scidiv/cores/autoloader.php';
include_once './ccny/scidiv/cores/components/Utils.php';
include_once './ccny/scidiv/cores/view/MainView.php';

use ccny\scidiv\cores\view\MainView as MainView;
use ccny\scidiv\cores\components\Utils as Utils;

$utils = Utils::getObject();

$rid = $utils->getRID();

$view = new MainView();

$arr_variables = ["rid" => $rid,"page_title"=>"DivOfScience - Conference Room Reservations","icon"=>"./images/scidivicon.ico"];

echo $view->render($arr_variables);



