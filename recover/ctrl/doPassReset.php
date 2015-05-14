<?php
	define("ERROR_FIELD_ERROR",1);
	define("ERROR_GENERIC_ERROR",2);
	define("NO_ERROR",0);

	include_once '../includes/UserManager.php';

	session_start();

	unset($_SESSION['err_msg']);
	unset($_SESSION['info_msg']);

	$cntr = new CoreLabsGenericController();
	$cntr->runJob();

	class CoreLabsGenericController
	{
			private $msg_sender;

			public function __construct()
			{
				
			}

			public function runJob()
			{

				/*
					Error object to store error information

					$err_obj->msg : Holds the message in human readable format;
					$err_obj->field : Holds the field to which error pertains. Could be empty;
					$err_obj->code : Holds the error code. 0 for no error;
					$err_obj->type : Indicated type of error. See defines above.

				*/

				$err_obj = $this->validateData();

				if( $err_obj->type )
					$this->failure($err_obj);



				try{

					$code = $_POST['v_code'];
					$password = $_POST['psw1'];

					$handler = new UserManager();

					$handler->doPasswordReset($code,$password);
				}
				catch(Exception $e)
				{
					//Send error message to the client

					$err_obj = new StdClass();
					$err_obj->field = "";
					$err_obj->code = $e->getCode();
					$err_obj->msg = $e->getMessage();
					$err_obj->type = ERROR_GENERIC_ERROR;

					$this->failure($err_obj);
				}

				$this->success();
			}


			private function validateData()
			{
				//This function will return an error object for each field that fails to validate

				$requiredFields  = array( "v_code"=>"Verification code not supplied",
											"psw1"=>"Password field is empty",
											"psw2"=>"Password field is empty",);


				//0 means there is no error
				$error_status = NO_ERROR;

				/*
					Initialize error object that will be returned by this function
				*/
				$error_obj = new stdClass();
				$error_obj->msg = "";
				$error_obj->field = "";
				$error_obj->code = 0;
				$error_obj->type = NO_ERROR;

				//check if required variables are defined
				foreach ($requiredFields as $key=>$value)
				{
					if( !isset($_POST[$key]) || $_POST[$key] == "" )
					{
						$error_obj->field = $key;
						$error_obj->msg = $value;
						$error_obj->type = ERROR_FIELD_ERROR;

						//return after each field that is found wrong
						return $error_obj;
					}
				}

				$pass_1 = $_POST['psw1'];
				$pass_2 = $_POST['psw2'];

				//Check if emails match
				if( strcmp( $pass_1,$pass_2 ) != 0)
				{
					$error_obj->field = "psw2";
					$error_obj->msg = "Passwords are different";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				/*
					Only check one password. If both passwords are thesame, we only need to check one of them.
				*/
				if ( !preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/', $pass_1) )
				{
					$error_obj->field = "psw1";
					$error_obj->msg = "Password must have a digit, lower and upper case charters. Min lenght is 6";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				return $error_obj;



			}

			private function success()
			{
				$_SESSION['info_msg'] = "Your password has been reset. Thank you.";
				header("Location: ./confirmpassreset.php",TRUE,303);
				die();

			}

			private function failure($error_obj)
			{

				$_SESSION['err_msg'] = $error_obj->msg;
				
				$code = "";

				if( isset($_POST['v_code']))
					$code = "?id=" . $_POST['v_code'];

				header('Location: ./resetpassword.php' . $code);
				die();
			}

	}














?>
