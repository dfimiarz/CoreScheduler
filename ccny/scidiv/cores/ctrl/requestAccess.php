<?php

namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../autoloader.php';
include_once __DIR__ . '/../view/JSONMessageSender.php';
include_once __DIR__ . '/../components/AccessRequestManager.php';
include_once __DIR__ . '/../model/CoreUser.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use ccny\scidiv\cores\view\JSONMessageSender as JSONMessageSender;
use ccny\scidiv\cores\components\AccessRequestManager as AccessRequestManager;
use ccny\scidiv\cores\model\CoreUser as CoreUser;

$msg_sender = new JSONMessageSender();

$session = new Session();
$session->start();

$request = Request::createFromGlobals();
$service_id = trim($request->request->get('id',null));

if( empty($service_id) )
{
    $msg_sender->onError(null,"No service selected");
}

/* @var $user CoreUser */
$user = $session->get('coreuser', null);

if( ! $user instanceof CoreUser )
{
    $user = new CoreUser('anonymous');
}


try{

	$handler = new AccessRequestManager();
	
	$handler->requestServiceAccess($user,$service_id);
	
}
catch(\Exception $e)
{
	$err_msg = "Operation failed: Error code " . $e->getCode();

	//Code 0 means that this is none-system error.
	//In this case we should be able to display the message text itself.
	if( $e->getCode() == 0 )
	{
		$err_msg = "Operation failed: ". $e->getMessage();
	}

	$msg_sender->onError(null,$err_msg);
}

$msg_sender->onResult(null,"OK");