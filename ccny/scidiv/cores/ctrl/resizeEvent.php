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

$msg_sender = new JSONMessageSender();

$session = new Session();
$session->start();

$request = Request::createFromGlobals();

/* @var $user ccny\scidiv\cores\model\CoreUser */
$user = $session->get('coreuser', null);

if( ! $user instanceof CoreUser )
{
    $user = new CoreUser('anonymous');
}


$record_id = trim($request->request->get('record_id',null));
$dayDelta = trim($request->request->get('dayDelta',null));
$minuteDelta = trim($request->request->get('minuteDelta',null));

try {

    $dayDelta = intval($dayDelta);
    $minuteDelta = intval($minuteDelta);
} catch (\Exception $e) {
    $msg_sender->onError(null, "New date info is not valid");
}

try {
    //Create the datahandler and insert the data
    $datahandler = new ScheduleDataHandler($user);
    $datahandler->resizeEvent($record_id, $dayDelta, $minuteDelta);
} catch (\Exception $e) {
    $err_msg = "Operation failed: Error code " . $e->getCode();

    //Code 0 means that this is none-system error.
    //In this case we should be able to display the message text itself.
    if ($e->getCode() == 0) {
        $err_msg = "Operation failed: " . $e->getMessage();
    }

    $msg_sender->onError(null, $err_msg);
}

$msg_sender->onResult(null, 'OK');
