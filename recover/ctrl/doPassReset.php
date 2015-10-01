<?php

include_once __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . '/../../ccny/scidiv/cores/config/config.php';
include_once __DIR__ . '/../../ccny/scidiv/cores/components/SystemConstants.php';

use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Session\Session as Session;
use ccny\scidiv\cores\components\UserManager as UserManager;

$pr = new PasswordResetter();
$pr->runJob();

class PasswordResetter {

    /** @var Request */
    private $request;

    /** @var Session */
    private $session;

    public function __construct() {
        $this->request = Request::createFromGlobals();

        $this->session = new Session();
        $this->session->start();

        $this->session->remove('err_msg');
        $this->session->remove('info_msg');
    }

    public function runJob() {

        /*
          Error object to store error information

          $err_obj->msg : Holds the message in human readable format;
          $err_obj->field : Holds the field to which error pertains. Could be empty;
          $err_obj->code : Holds the error code. 0 for no error;
          $err_obj->type : Indicated type of error. See defines above.

         */

        $err_obj = $this->validateData();

        if ($err_obj->type) {
            $this->failure($err_obj);
        }

        try {

            $code = $this->request->request->get('v_code');
            $password = $this->request->request->get('psw1');

            $handler = new UserManager();

            $handler->doPasswordReset($code, $password);
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

        $requiredFields = array("v_code" => "Verification code not supplied",
            "psw1" => "Password field is empty",
            "psw2" => "Password field is empty",);


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

        $pass_1 = $this->request->request->get('psw1');
        $pass_2 = $this->request->request->get('psw2');

        //Check if emails match
        if (strcmp($pass_1, $pass_2) != 0) {
            $error_obj->field = "psw2";
            $error_obj->msg = "Passwords are different";
            $error_obj->type = \VAL_FIELD_ERROR;

            return $error_obj;
        }

        /*
          Only check one password. If both passwords are thesame, we only need to check one of them.
         */
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/', $pass_1)) {
            $error_obj->field = "psw1";
            $error_obj->msg = "Password must have a digit, lower and upper case charters. Min lenght is 6";
            $error_obj->type = \VAL_FIELD_ERROR;

            return $error_obj;
        }

        return $error_obj;
    }

    private function success() {
        $this->session->set('info_msg',"Your password has been reset. Thank you.");
        header("Location: ./../success.php", TRUE, 303);
        exit();
    }

    private function failure($error_obj) {

        $this->session->set('err_msg',$error_obj->msg);

        $code = $this->request->request->get('v_code',null);

        $params = !empty($code) ? "?id=" . $code : "";

        header('Location: ./../newpassword.php' . $params);
        exit();
    }

}
