<?php

include_once __DIR__ . '/../../ccny/scidiv/cores/autoloader.php';
include_once __DIR__ . '/../../ccny/scidiv/cores/config/config.php';
include_once __DIR__ . '/../../ccny/scidiv/cores/components/SystemConstants.php';

use ReCaptcha\ReCaptcha as ReCaptcha;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Session\Session as Session;

$cntr = new CoreLabsController();
$cntr->runJob();

class CoreLabsController {

    /** @var ReCaptcha */
    private $recaptcha;

    /** @var Request */
    private $request;
    
    /** @var Session */
    private $session;

    public function __construct() {
        $this->recaptcha = new ReCaptcha(\RECAPTCHA_PRIV_KEY);
        $this->request = Request::createFromGlobals();

        $this->session = new Session();
        $this->session->start();

        $this->session->remove('err_msg');
        $this->session->remove('info_msg');
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

        $err_obj = $this->validateData();     
        
        if ($err_obj->type)
            $this->failure($err_obj);

        try {

            $user_info->email = trim($this->request->request->get('email'));

            $handler = new UserManager();

            $handler->doLoginReminder($user_info->email);
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

        $requiredFields = array("email" => "E-mail is missing or invalid", "g-recaptcha-response" => "Are you sure you are not a robot?");


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
            $value = $this->request->request->get($key);
            if (empty( $value )) {
                $error_obj->field = $key;
                $error_obj->msg = $requiredFields[$key];
                $error_obj->type = \VAL_FIELD_ERROR;

                //return after each field that is found wrong
                return $error_obj;
            }
        }

        $email = $this->request->request->get('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_obj->field = "email";
            $error_obj->msg = "E-mail is missing or invalid";
            $error_obj->type = \VAL_FIELD_ERROR;

            return $error_obj;
        }

        $resp = $this->recaptcha->verify($this->request->request->get('g-recaptcha-response'), $_SERVER['REMOTE_ADDR']);

        if (!$resp->isSuccess()) {
            $error_obj->field = "captcha";
            $error_obj->msg = "reCaptcha incorrect. Please try again";
            $error_obj->type = \VAL_FIELD_ERROR;

            //return after each field that is found wrong
            return $error_obj;
        }

        return $error_obj;
    }

    private function success() {
        $this->session->set('info_msg',"Request has been processed. Please check your email for instructions how to proceed.");
        header("Location: ./confirmloginrecovery.php", TRUE, 303);
        exit();
    }

    private function failure($error_obj) {
        
        $this->session->set('err_msg',$error_obj->msg);
        header('Location: ./../username.php');
        exit();
    }

}
