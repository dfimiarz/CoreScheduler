<?php

	include_once 'includes/ScheduleDataHandler.php';
	include_once 'includes/JSONMessageSender.php';

	$msg_sender = new JSONMessageSender();

	//Enable session support
	session_start();

	$record_id = null;
	$note_txt = null;

	if( isset($_POST['pk']))
		$record_id = $_POST['pk'];

	if( isset($_POST['value']))
		$note_txt = $_POST['value'];

	try{
		//Create the datahandler and insert the data
		$datahandler = new ScheduleDataHandler();
		$datahandler->changeNote($record_id,$note_txt);
	}
	catch(Exception $e)
	{
		$err_msg = "Operation failed: Error code " . $e->getCode();

		//Code 0 means that this is none-system error.
		//In this case we should be able to display the message text itself.
		if( $e->getCode() == 0 )
		{
			$err_msg = "Operation failed: ". $e->getMessage();
		}

		$msg_sender->onError(null,$err_msg);
	}

	$msg_sender->onResult(null,null);

?>