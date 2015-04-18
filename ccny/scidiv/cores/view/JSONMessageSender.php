<?php
/*
	Class used to send messages to the client
*/

namespace ccny\scidiv\cores\view;

include_once __DIR__ . '/../components/SystemConstants.php';
include_once 'MessageSender.php';


class JSONMessageSender extends MessageSender {


	public function sendMessage($response)
	{

		echo \json_encode($response);
		exit();

	}


}