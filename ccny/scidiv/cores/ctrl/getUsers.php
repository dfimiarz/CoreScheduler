<?php

namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../autoloader.php';
include_once __DIR__ . '/../model/ScheduleDataHandler.php';
include_once __DIR__ . '/../model/CoreUser.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\ScheduleDataHandler as ScheduleDataHandler;

$session = new Session();
$session->start();

$request = Request::createFromGlobals();
$record_id = $request->request->get('rec_id',null);

/* @var $user ccny\scidiv\cores\model\CoreUser */
$user = $session->get('coreuser', null);

if( ! $user instanceof CoreUser )
{
    $user = new CoreUser('anonymous');
}

try {
    //Create the datahandler and insert the data
    $datahandler = new ScheduleDataHandler($user);
    $users = $datahandler->getAuthorizedUsers($record_id);
} catch (Exception $e) {
    $err_msg = "Operation failed: Error code " . $e->getCode();

    //Code 0 means that this is none-system error.
    //In this case we should be able to display the message text itself.
    if ($e->getCode() == 0) {
        $err_msg = "Operation failed: " . $e->getMessage();
    }

    onError($err_msg);
}

onResult($users, "OK");

//return error
function onError($message) {
    header('HTTP 400 Bad Request', true, 400);
    echo $message;

    exit();
}

//return result. Specially formatted for the xeditable library
function onResult($data, $message) {

    echo json_encode($data);

    exit();
}