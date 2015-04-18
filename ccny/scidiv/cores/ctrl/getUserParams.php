<?php
namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../autoloader.php';
include_once __DIR__ . '/../view/JSONMessageSender.php';
include_once __DIR__ . '/../model/SystemStateManager.php';
include_once __DIR__ . '/../model/CoreUser.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use ccny\scidiv\cores\view\JSONMessageSender as JSONMessageSender;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\SystemStateManager as SystemStateManager;

$session = new Session();
$session->start();

$request = Request::createFromGlobals();

$msg_sender = new JSONMessageSender();


//Define calandar configurations to send to the client
$config = new \stdClass();

//Text to display
$config->txt = '';

//Is user authorized
$config->can_add = 0;

//Show text?
$config->show_txt = 0;

//Show request btn?
$config->show_req_btn = 0;

$service_id = $request->request->get('serv_id',NULL);
if(is_null($service_id))
{
	$msg_sender->onResult($config,'OK');
}
/* @var $user CoreUser */
$user = $session->get('coreuser', null);

if( ! $user instanceof CoreUser )
{
    $user = new CoreUser('anonymous');
}

try {

    $system_manager = new SystemStateManager();
    $config = $system_manager->getUserSessionConfig($service_id, $user);
} catch (\Exception $e) {

    $err_msg = "Operation failed: Error code " . $e->getCode();

    //Code 0 means that this is none-system error.
    //In this case we should be able to display the message text itself.
    if ($e->getCode() == 0) {
        $err_msg = "Operation failed: " . $e->getMessage();
    }

    $msg_sender->onError(null, $err_msg);
}

$msg_sender->onResult($config, 'OK');
