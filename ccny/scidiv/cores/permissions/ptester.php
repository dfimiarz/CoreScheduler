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

include_once __DIR__ . '/PermissionManager.php';
include_once __DIR__ . '/EventPermToken.php';
include_once __DIR__ . '/../components/DbConnectInfo.php';
include_once __DIR__ . '/../components/SystemConstants.php';

use ccny\scidiv\cores\permissions\PermissionManager as PermissionManager;
use ccny\scidiv\cores\permissions\PermissionToken as PermissionToken;
use ccny\scidiv\cores\permissions\EventPermToken as EventPermToken;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;

echo "Testing permission";
$dbinfo = DbConnectInfo::getDBConnectInfoObject();

@$connection = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

$roles = array(ROLE_AUTHENTICATED);
$service_state = array(SERVICE_STATE_ACTIVE);
$event_state = array(EVENT_FUTURE);

$token = new EventPermToken($roles,$service_state,$event_state);

echo $token->getJSON();

$pmngr = new PermissionManager($connection);

$time_start = microtime(true);

for( $i = 0; $i < 1000; $i++)
{
    echo $pmngr->checkPermission(1,$token);
}

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Checked perm in $time seconds\n";




