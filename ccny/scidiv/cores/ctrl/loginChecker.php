<?php
namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../autoloader.php';
include_once __DIR__ . '/../model/LoginManager.php';
include_once __DIR__ . '/../view/JSONMessageSender.php';
include_once __DIR__ . '/../model/CoreUser.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use ccny\scidiv\cores\view\JSONMessageSender as JSONMessageSender;
use ccny\scidiv\cores\model\CoreUser as CoreUser;

$session = new Session();
$session->start();

$msg_sender = new JSONMessageSender();

$user = $session->get('coreuser', null);

$ret_user_info = null;

if ($user instanceof CoreUser) {
    $ret_user_info = new \stdClass();

    $ret_user_info->name = $user->user_name;
    $ret_user_info->last_active = $user->last_active;
    $ret_user_info->pi = $user->pi_name;
    $ret_user_info->type = $user->user_type;
}


//return user info for the login panel
$msg_sender->onResult($ret_user_info, 'OK');