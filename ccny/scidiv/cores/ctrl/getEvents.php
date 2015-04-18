<?php

namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../autoloader.php';
include_once __DIR__ . '/../view/JSONMessageSender.php';
include_once __DIR__ . '/../model/ScheduleDataHandler.php';
include_once __DIR__ . '/../model/CoreUser.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use ccny\scidiv\cores\view\JSONMessageSender as JSONMessageSender;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\ScheduleDataHandler as ScheduleDataHandler;

$session = new Session();
$session->start();

$msg_sender = new JSONMessageSender();

$user = $session->get('coreuser', null);

if( ! $user instanceof CoreUser )
{
    $user = new CoreUser('anonymous');
}
/*
 * Get $_POST parameters
 * Make sure they are not empty
 */
$request = Request::createFromGlobals();
$start = $request->request->get('start',null);
$end = $request->request->get('end',null);
$eq_id = $request->request->get('eq_id',0);

$event_options = new \stdClass();

$event_options->start = $start;
$event_options->end = $end;
$event_options->eq_id = $eq_id;
$event_options->user = $user;

try {
    
    $data_handler = new ScheduleDataHandler($user);
    $data = $data_handler->getEventsByEq($event_options);
    
} catch (\Exception $e) {
    $err_msg = "Operation failed: Error code " . $e->getCode();

    //Code 0 means that this is none-system error.
    //In this case we should be able to display the message text itself.
    if ($e->getCode() == 0) {
        $err_msg = "Operation failed: " . $e->getMessage();
    }

    $msg_sender->onError(null, $err_msg);
}

$msg_sender->onResult($data, "OK");

