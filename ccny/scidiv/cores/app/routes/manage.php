<?php

use Silex\ControllerCollection;
use Silex\Controller;

/* 
 * The MIT License
 *
 * Copyright 2017 Daniel Fimiarz <dfimiarz@ccny.cuny.edu>.
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

/* @var $manage ControllerCollection */
$manage = $app['controllers_factory'];
$manage->get('/', function () { return 'Manage content'; });

$manage->get("/accounts", 'ccny\\scidiv\\cores\\ctrl\\AccountController::index')->bind('account_home');

/* @var $account_details Controller */
$account_details = $manage->get("/accounts/{acc_id}", 'ccny\\scidiv\\cores\\ctrl\\AccountController::details');
$account_details->assert('acc_id', '\d+');
$account_details->bind('account_details');

$manage->get("/accounts/find", 'ccny\\scidiv\\cores\\ctrl\\AccountController::find')->bind('account_find');

$manage->get("/accounts/pending", 'ccny\\scidiv\\cores\\ctrl\\AccountController::listPending')->bind('account_pending');

return $manage;
