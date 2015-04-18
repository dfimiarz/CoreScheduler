<?php

namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../view/JSONMessageSender.php';

use ccny\scidiv\cores\view\JSONMessageSender as JSONMessageSender;

$msg_sender = new JSONMessageSender();

session_start();
session_unset();
session_destroy();


$msg_sender->onResult(null,'OK');
