<?php
namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../autoloader.php';
include_once __DIR__ . '/../model/LoginManager.php';
include_once __DIR__ . '/../model/CoreUser.php';
include_once __DIR__ . '/../view/JSONMessageSender.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use ccny\scidiv\cores\view\JSONMessageSender as JSONMessageSender;
use ccny\scidiv\cores\model\LoginManager as LoginManager;
use ccny\scidiv\cores\model\CoreUser as CoreUser;

//Initialize session
$session = new Session();
$session->start();
$session->migrate(true);

$msg_sender = new JSONMessageSender();


/*
 * Get $_POST parameters
 * Make sure they are not empty
 */
$request = Request::createFromGlobals();
$username = trim($request->request->get('user',null));
$password = trim($request->request->get('pass',null));

if( empty($username) || empty($password) )
{
    $msg_sender->onError(null, "User name or password cannot be empty");
}

$user = new CoreUser($username);

$login_manager = new LoginManager($user);

try {
    $login_manager->authenticateUser($password);   
}
catch( \Exception $e)
{
    $msg_sender->onError(null, "Authentication failed. ERROR ID:  ". $e->getCode());
}

if(! $user->isAuth())
{
    $msg_sender->onError(null, "Could not log in. Please check your password and try again." );
}


try{
    
    if( ! $login_manager->getAccountInfo())
    {
        throw new \Exception("Could not locate user account", 0);
    }
        
} catch (\Exception $e) {
    $err_msg = "Login failed: Error code " . $e->getCode();

    //Code 0 means that this is none-system error.
    //In this case we should be able to display the message text itself.
    if ($e->getCode() == 0) {
        $err_msg = "Login failed: " . $e->getMessage();
    }

    $msg_sender->onError(null, $err_msg);
}

$session->set('coreuser', $user);

$ret_user_info = new \stdClass();

$ret_user_info->name = $user->user_name;
$ret_user_info->last_active = $user->last_active;
$ret_user_info->pi = $user->pi_name;
$ret_user_info->type = $user->user_type;

//return user info for the login panel
$msg_sender->onResult($ret_user_info, 'OK');
