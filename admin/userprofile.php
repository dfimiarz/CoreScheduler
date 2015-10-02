<?php

include_once __DIR__ . '/../ccny/scidiv/cores/admin/autoloader.php';

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

$user_details = $ctrl->getUserDetails($enc_user_id);

if (!$user_details instanceof UserDetails) {
    $user_details = new UserDetails();
}


$view = new CoreView(__DIR__ . '/../ccny/scidiv/cores/admin/view/templates');
$view->loadTemplate('user_profile.html.twig');

$template_vars = [
    "fname"=>$user_details->name,
    "uname"=>$user_details->username,
    "email"=>$user_details->email,
    "phone"=>$user_details->phone,
    "mentor"=>$user_details->mentor,
    "type"=>$user_details->type,
    "dt_active"=>$user_details->lastactive,
    "note"=>$user_details->note
];

echo $view->render($template_vars);
