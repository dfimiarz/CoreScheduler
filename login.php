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

include_once './ccny/scidiv/cores/autoloader.php';
include_once './ccny/scidiv/cores/components/Utils.php';
include_once './ccny/scidiv/cores/config/config.php';
include_once './ccny/scidiv/cores/view/CoreView.php';

use ccny\scidiv\cores\view\CoreView as CoreView;

$view = new CoreView();
$view->loadTemplate('login.html.twig');

$arr_variables = ["error_txt"=>"Login failed","page_title"=>"DivOfScience - Conference Room Reservations","icon"=>SYSTEM_ICON];

echo $view->render($arr_variables);


/*
if( isset($_SESSION['login_err']))
{
	if( ! empty($_SESSION['login_err']))
	{
		$is_error = TRUE;
		$error_txt = $_SESSION['login_err'];

		unset($_SESSION['login_err']);
	}
}


//See if the $_GET['src'] is set. If so, pass it to log in controller for redirect.
unset($_SESSION['dest']);

if( isset($_GET['src']) && ! empty($_GET['src']))
{
	$_SESSION['dest'] = $_GET['src'];
}
*/

