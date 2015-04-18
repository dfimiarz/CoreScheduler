<?php

/*
	===CHANGE IN CODING STYLE===
	This class implements connection to the server using object oriented style
*/

/*
	===CHANGE LOG===
	08/27/2013 -  registerUser() uses a mysql stored procedure to insert data to the DB. See the function for details
	12/04/2013 -  added checkEmail() to make sure that user account have unique emails
*/


include_once 'DbConnectInfo.php';
include_once 'Logger.php';

class RegistrationManager
{

	//Placeholder for mysqli object
	private $mysqli;

	//Logging class
	private $logger;

	//Class constructor
	function __construct()
	{


		$dbinfo = DbConnectInfo::getDBConnectInfoObject();

		$this->mysqli = new mysqli($dbinfo->getServer(),$dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(),$dbinfo->getPort());

		if ($this->mysqli->connect_errno) {
		    $this->throwCustomExceptionOnError( $this->mysqli->connect_errno, $this->mysqli->connect_error);
		}

		$this->logger = Logger::getLogger();
	}

	function __destruct()
	{

		if( $this->mysqli )
			$this->mysqli->close();

	}

	public function registerUser($user_info)
	{

		/*
			---MYSQL STORED PROCEDURE DETAILS---

			add_new_user(IN first_name VARCHAR(64),IN last_name VARCHAR(64),IN phone VARCHAR(32),IN email VARCHAR(128),IN username VARCHAR(16),IN pass VARCHAR(45),IN pi_name VARCHAR(64), IN  pi_email VARCHAR(128) , IN pi_phone VARCHAR(16), IN  pi_address1 VARCHAR(128), IN pi_address2 VARCHAR(128), IN pi_city VARCHAR(32), IN  pi_state VARCHAR(2), IN pi_zip VARCHAR(5))
		*/

		$insert_proc_call = "CALL add_new_user(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

		if(!($stmt = $this->mysqli->prepare($insert_proc_call)))
		{
			$this->throwDBExceptionOnError($this->mysqli->errno,$this->mysqli->error);
		}

		if(!($stmt->bind_param("ssssssssssssss", $user_info->fname,$user_info->lname,$user_info->phone,$user_info->email,$user_info->uname,$user_info->psw,$user_info->pi_name,$user_info->pi_email,$user_info->pi_phone,$user_info->pi_address_1,$user_info->pi_address_2,$user_info->pi_city,$user_info->pi_state,$user_info->pi_zip)))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		if(!($stmt->execute()))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		$stmt->close();

		$this->logger->log("ACTIVITY: registerUser() - User Added",ACTIVITY_LOG_TYPE);

		return 1;

	}

	public function checkUserName($username)
	{
		/*
			This function checks if a given user name alrady exisits.
			Returns TRUE if it does, FALSE if it does not exist.
		*/

		$query = "SELECT * FROM core_users WHERE username = ?";

		if(!($stmt = $this->mysqli->prepare($query)))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		if(!($stmt->bind_param("s",$username)))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		if(!($stmt->execute()))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		if(!($stmt->store_result()))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		$user_count = $stmt->num_rows;

		$stmt->close();

		if( $user_count == 0 )
			return FALSE;

		return TRUE;

	}

	public function checkEmail($email)
	{
		/*
			This function checks if a given user name alrady exisits.
			Returns TRUE if it does, FALSE if it does not exist.
		*/

		$query = "SELECT * FROM core_users WHERE email = ?";

		if(!($stmt = $this->mysqli->prepare($query)))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		if(!($stmt->bind_param("s",$email)))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		if(!($stmt->execute()))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		if(!($stmt->store_result()))
		{
			$this->throwDBExceptionOnError($stmt->errno,$stmt->error);
		}

		$user_count = $stmt->num_rows;

		$stmt->close();

		if( $user_count == 0 )
			return FALSE;

		return TRUE;

	}

	private function throwDBExceptionOnError($errno,$errmsg) {
		$this->logger->log($errmsg,ERROR_LOG_TYPE);
		throw new Exception($errmsg,$errno);
	}


	private function throwCustomExceptionOnError($errno = 0 ,$errmsg) {
		$this->logger->log($errmsg,ERROR_LOG_TYPE);
		throw new Exception($errmsg,$errno);
	}

}


?>