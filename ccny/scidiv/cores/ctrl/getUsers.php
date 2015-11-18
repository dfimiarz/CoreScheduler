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
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\ScheduleDataHandler as ScheduleDataHandler;
use ccny\scidiv\cores\components\SystemException as SystemException;

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
} 
catch (SystemException $e){
    
    $client_error = $e->getUIMsg();
    
    if( empty($client_error)){
        $client_error = "Operation failed: Error code " . $e->getCode();
    }
    
    onError($client_error);
}
catch (Exception $e) {
    $err_msg = "Unexpected error:  " . $e->getCode();
    $msg_sender->onError(null, $err_msg);
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