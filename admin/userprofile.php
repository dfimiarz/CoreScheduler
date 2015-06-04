<?php

include_once __DIR__ . '/../ccny/scidiv/cores/admin/autoloader.php';
include_once __DIR__ . '/../ccny/scidiv/cores/config/config.php';
include_once __DIR__ . '/../ccny/scidiv/cores/view/CoreView.php';

use ccny\scidiv\cores\view\CoreView as CoreView;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Session\Session as Session;
use ccny\scidiv\cores\admin\ctrl\UserDetailsCtrl as UserDetailsCtrl;
use ccny\scidiv\cores\admin\model\UserDetails as UserDetails;
use ccny\scidiv\cores\model\CoreUser as CoreUser;

$request = Request::createFromGlobals();
$session = new Session();
$session->start();

/* @var $user CoreUser */
$logged_in_user = $session->get('coreuser', null);

if( ! $logged_in_user instanceof CoreUser )
{
    $logged_in_user = new CoreUser('anonymous');
}

/* @var $ctrl UserDetailsCtrl */
$ctrl = new UserDetailsCtrl($logged_in_user);

$enc_user_id = $request->query->get('uid',null);

/* @var $user_details UserDetails */
$user_details = $ctrl->getUserDetails($enc_user_id);


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
