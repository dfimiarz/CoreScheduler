<?php

include_once 'includes/LoginManager.php';
include_once 'includes/JSONMessageSender.php';

$msg_sender = new JSONMessageSender();


//restore the session and
session_start();


//Get basic info for the selected service
//Info returned will include:
//User user_role_name,cost,webpage_link,number_pis,number_users,usage_data,hourly_rate

if (!isset($_POST['service_id'])) {
    $msg_sender->onError(null, "Error: No service selected.");
}

$service_info = new stdClass();

if( isset( $_SESSION['user_id'] ))
{
	try{

		$login_manager = LoginManager::getLoginManager();

		$user_info = $login_manager->getLoggedInUserInfo();

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
}

$ret_user_info = new stdClass();

if( $user_info == NULL )
	$ret_user_info = $user_info;
else
{
	if( isset($user_info->name) )
		$ret_user_info->name = $user_info->name;
	if( isset($user_info->last_active) )
		$ret_user_info->last_active = $user_info->last_active;
	if( isset($user_info->pi) )
		$ret_user_info->pi = $user_info->pi;
	if( isset($user_info->type) )
		$ret_user_info->type = $user_info->type;
}

//return user info for the login panel
$msg_sender->onResult($ret_user_info,'OK');


?>