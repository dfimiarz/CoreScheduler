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
include_once __DIR__ . '/../components/SystemConstants.php';

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

//Expecting unix time stamp is s_time and e_time
$start_time = trim($request->request->get('start',null));
$end_time = trim($request->request->get('end',null));
$all_day = trim($request->request->get('allday',0));
$service_id = trim($request->request->get('service',null));

//validate the service_id
if (!filter_var($service_id, FILTER_VALIDATE_INT))
    $msg_sender->onError(null, "Service ID is not valid");

$event_options = new \stdClass();
$event_options->start = $start_time;
$event_options->end = $end_time;
$event_options->service_id = $service_id;
$event_options->all_day = $all_day;

try {

    //Set up date and time. POST is expected in UNIX timestamp
    $start_dt = new \DateTime();
    $start_dt->setTimestamp($event_options->start);
    $end_dt = new \DateTime();
    $end_dt->setTimestamp($event_options->end);

    //if this is an allday event, modify the end time by adding 1 day
    if ($all_day) {
        //This function needs php >= 5.3
        $end_dt->add(new \DateInterval('P1D'));
        $event_options->end = $end_dt->getTimestamp();
    }
} catch (\Exception $e) {
    $msg_sender->onError(null, "One of dates is not valid");
}

try {
    
    //Create the datahandler and insert the data
    $datahandler = new ScheduleDataHandler($user);
    $datahandler->createEvent($event_options);
    
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