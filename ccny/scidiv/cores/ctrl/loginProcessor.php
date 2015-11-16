<?php

/*
 * The MIT License
 *
 * Copyright 2015 Daniel Fimiarz <dfimiarz@ccny.cuny.edu>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../../../../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use ccny\scidiv\cores\view\JSONMessageSender as JSONMessageSender;
use ccny\scidiv\cores\model\LoginManager as LoginManager;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\components\SystemException as SystemException;

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
        
}
catch (SystemException $e){
    
    $client_error = $e->getUIMsg();
    
    if( empty($client_error)){
        $client_error = "Operation failed: Error code " . $e->getCode();
    }
    
    $msg_sender->onError(null, $client_error);
}
catch (\Exception $e) {
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
