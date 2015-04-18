<?php
/*
	Class used to send messages to the client
*/

namespace ccny\scidiv\cores\view;

include_once __DIR__ . '/../components/SystemConstants.php';

interface iMessageSender
{
   	function sendMessage($response);
}

class MessageSender implements iMessageSender {

	public function __construct()
	{

	}

	public function onError($data = null, $msg, $err_type = 1)
	{
		$response = new \stdClass();

		$response->error = $err_type;
		$response->message = $msg;
		$response->data = $data;

		$this->sendMessage($response);

	}

	public function onResult($data = null, $msg )
	{
		$response = new \stdClass();

		$response->error = 0;
		$response->message = "OK";
		$response->data = $data;

		$this->sendMessage($response);

	}

	public function sendMessage($response)
	{
		print_r($response);
		exit();
	}


}