<?php
namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../autoloader.php';
include_once __DIR__ . '/../components/EventDetailsHandler.php';
include_once __DIR__ . '/../model/CoreUser.php';
include_once __DIR__ . '/../view/SessionDetailsView.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\components\EventDetailsHandler as EventDetailsHandler;
use ccny\scidiv\cores\view\SessionDetailsView as SessionDetailsView;

$session = new Session();
$session->start();

$html = "";

$request = Request::createFromGlobals();
$encrypted_event_id = $request->request->get('id',null);

/* @var $user ccny\scidiv\cores\model\CoreUser */
$user = $session->get('coreuser', null);

if( ! $user instanceof CoreUser )
{
    $user = new CoreUser('anonymous');
}

try {

    $handler = new EventDetailsHandler($user);
    $ArrDetails = $handler->getEventDetails($encrypted_event_id);

    if (!is_array($ArrDetails)) {
        $ArrDetails = [];
    }
    
    $view = new SessionDetailsView();

    $html = $view->render($ArrDetails);
    
} catch(\Exception $e)
{
	$err_msg = "Fetching data failed: Error code " . $e->getCode();

	//Code 0 means that this is none-system error.
	//In this case we should be able to display the message text itself.
	if( $e->getCode() == 0 )
	{
		$err_msg = "Fetching data failed: ". $e->getMessage();
	}

	$html = $err_msg;
}

echo $html;
