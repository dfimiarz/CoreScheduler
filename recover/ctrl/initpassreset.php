<?php

include_once __DIR__ . '/../../ccny/scidiv/cores/autoloader.php';
include_once __DIR__ . '/../../ccny/scidiv/cores/components/SystemConstants.php';

use ccny\scidiv\cores\components\UserManager as UserManager;

session_start();

unset($_SESSION['err_msg']);
unset($_SESSION['info_msg']);

$cntr = new CoreLabsController();
$cntr->runJob();

class CoreLabsController {

    public function __construct() {
        
    }

    public function runJob() {
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

        if ($error_obj->type)
            $this->failure($error_obj);

        try {

            $user_info->uname = $_POST["uname"];

            $handler = new UserManager();

            $handler->initPasswordReset($user_info->uname);
            
        } catch (Exception $e) {
            //Send error message to the client

            $err_obj = new StdClass();
            $err_obj->field = "";
            $err_obj->code = $e->getCode();
            $err_obj->msg = $e->getMessage();
            $err_obj->type = \VAL_SYSTEM_ERROR;

            $this->failure($err_obj);
        }




        $this->success();
    }

    private function validateData() {
        //This function will return an error object for each field that fails to validate

        $requiredFields = array("uname" => "Username is missing or invalid",);


        //0 means there is no error
        $error_status = \VAL_NO_ERROR;

        /*
          Initialize error object that will be returned by this function
         */
        $error_obj = new stdClass();
        $error_obj->msg = "";
        $error_obj->field = "";
        $error_obj->code = 0;
        $error_obj->type = \VAL_NO_ERROR;

        //check if required variables are defined
        foreach ($requiredFields as $key => $value) {
            if (!isset($_POST[$key]) || $_POST[$key] == "") {
                $error_obj->field = $key;
                $error_obj->msg = $value;
                $error_obj->type = \VAL_FIELD_ERROR;

                //return after each field that is found wrong
                return $error_obj;
            }
        }

        if (trim($_POST["uname"]) == "") {
            $error_obj->field = "uname";
            $error_obj->msg = "User name is empty";
            $error_obj->type = \VAL_FIELD_ERROR;
            $err_obj->code = 0;

            return $error_obj;
        }

        return $error_obj;
    }

    private function success() {
        $_SESSION['info_msg'] = "Request has been processed. Please check your email for instructions how to proceed.";
        header("Location: ../confirmpassrecover.php", TRUE, 303);
        die();
    }

    private function failure($error_obj) {
        $_SESSION['err_msg'] = $error_obj->msg;
        header('Location: ./../password.php');
        die();
    }

}
