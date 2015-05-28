<?php

include_once __DIR__ . '/../ccny/scidiv/cores/admin/autoloader.php';
include_once __DIR__ . '/../ccny/scidiv/cores/config/config.php';
include_once __DIR__ . '/../ccny/scidiv/cores/view/CoreView.php';

use ccny\scidiv\cores\view\CoreView as CoreView;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Session\Session as Session;

$request = Request::createFromGlobals();
$session = new Session();
$session->start();

$user_id = $request->query->get('uid',null);

$view = new CoreView(__DIR__ . '/../ccny/scidiv/cores/admin/view/templates');
$view->loadTemplate('user_profile.html.twig');

$template_vars = [
    "fname"=>"John Doe",
    "uname"=>"jdoe",
    "email"=>"jdoe@ok.com",
    "phone"=>"(646) 238-2087",
    "mentor"=>"William Smith",
    "type"=>"Internal",
    "dt_active"=>"01/01/2015",
    "note"=>"This is a test note. Not sure how long it can be. Wrapping should be tested just in case it is too long to fit. It is also good to test event longer text"
    
];

echo $view->render($template_vars);
