<?php

	/*
		===CHANGE LOG===

		12/04/2013 - Added code to check for user email to ensure that each account has a different email.

	*/

	define("ERROR_FIELD_ERROR",1);
	define("ERROR_GENERIC_ERROR",2);
	define("NO_ERROR",0);

	include_once 'includes/RegistrationManager.php';
	include_once 'includes/JSONMessageSender.php';

	$cntr = new RegistrationControler();
	$cntr->registerUser();

	class RegistrationControler
	{
			private $msg_sender;

			public function __construct()
			{
				$this->msg_sender = new JSONMessageSender();
			}

			public function registerUser()
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

				$error_obj = $this->validateData();

				if( $error_obj->type )
					$this->sendError($error_obj);

				try{
					$user_info = $this->extractUserInfo();

					$handler = new RegistrationManager();

					if($handler->checkUserName($user_info->uname))
					{
						$err_obj = new StdClass();
						$err_obj->msg = "User name already exists";
						$err_obj->field = "uname";
						$err_obj->code = 0;
						$err_obj->type = ERROR_FIELD_ERROR;

						$this->sendError($err_obj);
					}

					//Check if the email is already in the sytem
					if($handler->checkEmail($user_info->email))
					{
						$err_obj = new StdClass();
						$err_obj->msg = "An account with this e-mail address already exists";
						$err_obj->field = "email1";
						$err_obj->code = 0;
						$err_obj->type = ERROR_FIELD_ERROR;

						$this->sendError($err_obj);
					}

					$handler->registerUser($user_info);
				}
				catch(Exception $e)
				{
					//Send error message to the client

					$err_obj = new StdClass();
					$err_obj->msg = $e->getMessage();
					$err_obj->field = "";
					$err_obj->code = $e->getCode();
					$err_obj->type = ERROR_GENERIC_ERROR;

					$this->sendError($err_obj);
				}

				$this->msg_sender->onResult(null,null);

			}


			private function validateData()
			{
				//This function will return an error object for each field that fails to validate

				$requiredFields  = array(	"uname"=>"Username is missing or invalid",
											"psw1"=>"Password is missing or invalid",
											"psw2"=>"Password is missing or invalid",
											"fname"=>"First Name is missing or invalid",
											"lname"=>"Last Name is missing or invalid",
											"phone"=>"Phone is missing or invalid",
											"email1"=>"Email is missing or invalid",
											"email2"=>"Email is missing or invalid",
											"pi_name"=>"PI name missing or invalid",
											"pi_email"=>"PI e-mail missing or invalid",
											"pi_phone"=>"PI phone missing or invalid",
											"pi_address_1"=>"PI address missing or invalid",
											"pi_city"=>"PI city missing or invalid",
											"pi_state"=>"PI state missing or invalid",
											"pi_zip"=>"PI zip code missing or invalid",
											);


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

				//Validate username
				$uname = $_POST['uname'];

				if ( !preg_match('/^[A-Za-z][A-Za-z0-9]{5,15}$/', $uname) )
				{
					$error_obj->field = "uname";
					$error_obj->msg = "Username must be 6-16 chars long and start with a letter";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				$phone = $_POST['phone'];

				if ( !preg_match('/^\(\d{3}\) \d{3}-\d{4}$/', $phone) )
				{
					$error_obj->field = "phone";
					$error_obj->msg = "Phone number format should be (999) 999-9999";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				$fname = $_POST['fname'];

				if ( strlen($fname) < 2 )
				{
					$error_obj->field = "fname";
					$error_obj->msg = "First name must be at least 2 characters long";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				$lname = $_POST['lname'];

				if ( strlen($lname) < 2 )
				{
					$error_obj->field = "lname";
					$error_obj->msg = "Last name must be at least 2 characters long";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				$email_1 = $_POST['email1'];
				$email_2 = $_POST['email2'];


				//Check if emails match
				if( strcmp( $email_1,$email_2 ) != 0)
				{
					$error_obj->field = "email2";
					$error_obj->msg = "E-mail addresses are different";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				/*
					Only check one email. If both emails are thesame, we only need to check one of them.
				*/
				if(!  filter_var($email_1, FILTER_VALIDATE_EMAIL))
				{
					$error_obj->field = "email1";
					$error_obj->msg = "E-mail is invalid";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
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

				$pi_name = $_POST['pi_name'];

				if ( strlen($pi_name) < 3 )
				{
					$error_obj->field = "pi_name";
					$error_obj->msg = "PI name must be at least 3 characters long";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				$pi_email = $_POST['pi_email'];

				if(!  filter_var($pi_email, FILTER_VALIDATE_EMAIL))
				{
					$error_obj->field = "pi_email";
					$error_obj->msg = "PI e-mail is invalid";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				$pi_phone = $_POST['pi_phone'];

				if ( !preg_match('/^\(\d{3}\) \d{3}-\d{4}$/', $pi_phone) )
				{
					$error_obj->field = "pi_phone";
					$error_obj->msg = "Phone number format should be (999) 999-9999";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				$pi_city = $_POST['pi_city'];

				if ( strlen( $pi_city ) < 2 )
				{
					$error_obj->field = "pi_city";
					$error_obj->msg = "City must be at least 2 charcters long";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}

				$pi_state = $_POST['pi_state'];

				if ( !preg_match('/^[A-Z]{2}$/', $pi_state))
				{
					$error_obj->field = "pi_state";
					$error_obj->msg = "Please select a valid state";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}


				$pi_zip = $_POST['pi_zip'];

				if ( !preg_match('/^\d{5}$/', $pi_zip) )
				{
					$error_obj->field = "pi_zip";
					$error_obj->msg = "Zip code must be 5 digits";
					$error_obj->type = ERROR_FIELD_ERROR;

					return $error_obj;
				}


				return $error_obj;

			}

			private function sendError($error_obj)
			{
				$this->msg_sender->onError($error_obj,'',$error_obj->type);
			}

			private function extractUserInfo()
			{
				$user_info = new stdClass();

				$user_info->uname = $_POST['uname'];
				$user_info->psw = $_POST['psw1'];
				$user_info->email = $_POST['email1'];
				$user_info->fname = $_POST['fname'];
				$user_info->lname = $_POST['lname'];
				$user_info->phone = $_POST['phone'];
				$user_info->pi_name = $_POST['pi_name'];
				$user_info->pi_email = $_POST['pi_email'];
				$user_info->pi_phone = $_POST['pi_phone'];
				$user_info->pi_address_1 = $_POST['pi_address_1'];
				$user_info->pi_address_2 = $_POST['pi_address_2'];
				$user_info->pi_city = $_POST['pi_city'];
				$user_info->pi_state = $_POST['pi_state'];
				$user_info->pi_zip = $_POST['pi_zip'];

				return $user_info;

			}
	}














?>
