<?php
	define("ERROR_GENERIC_ERROR",1);
	define("NO_ERROR",0);

	include_once '../includes/UserManager.php';
	include_once '../includes/recaptchalib.php';

	session_start();

	unset($_SESSION['err_msg']);
	unset($_SESSION['info_msg']);

	$cntr = new CoreLabsController();
	$cntr->runJob();

	class CoreLabsController
	{
			private $msg_sender;
			private $recaptcha_private_key = "6LdXAgMTAAAAADs_rsYCipgXolAFu7kTjXtpGGsZ";

			public function __construct()
			{

			}

			public function runJob()
			{
				//Object to keep user information
				$user_info = new stdClass();

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

					$user_info->email  = trim($_POST["email"]);


					$handler = new UserManager();

					$handler->doLoginReminder($user_info->email);

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

				$requiredFields  = array( "email"=>"E-mail is missing or invalid", "recaptcha_challenge_field" => "reCapchta challange field is missing", "recaptcha_response_field" =>"reCapchta reponse field is missing");


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
						$error_obj->type = ERROR_GENERIC_ERROR;

						//return after each field that is found wrong
						return $error_obj;
					}
				}

				if(!  filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
				{
					$error_obj->field = "email";
					$error_obj->msg = "E-mail is missing or invalid";
					$error_obj->type = ERROR_GENERIC_ERROR;

					return $error_obj;
				}

				$resp = recaptcha_check_answer ($this->recaptcha_private_key,
				                                $_SERVER["REMOTE_ADDR"],
				                                $_POST["recaptcha_challenge_field"],
				                                $_POST["recaptcha_response_field"]);

				if (!$resp->is_valid) {
					$error_obj->field = "captcha";
					$error_obj->msg = "reCaptcha incorrect. Please try again";
					$error_obj->type = ERROR_GENERIC_ERROR;

					//return after each field that is found wrong
					return $error_obj;
				}

				return $error_obj;

			}

			private function success()
			{
				$_SESSION['info_msg'] = "Request has been processed. Please check your email for instructions how to proceed.";
				header("Location: ./confirmloginrecovery.php",TRUE,303);
				die();

			}

			private function failure($error_obj)
			{
				$_SESSION['err_msg'] = $error_obj->msg;
				header('Location: ./recoverusername.php');
				die();
			}



	}














?>
