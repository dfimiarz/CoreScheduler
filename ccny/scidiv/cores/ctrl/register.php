<?php

namespace ccny\scidiv\cores\components;

include_once __DIR__ . '/../../../../vendor/autoload.php';

use ccny\scidiv\cores\view\JSONMessageSender as JSONMessageSender;
use Symfony\Component\HttpFoundation\Request as Request;
use ccny\scidiv\cores\model\ValidationError as ValidationError;
use ccny\scidiv\cores\model\CoreUserRegistrant as CoreUserRegistrant;
use ccny\scidiv\cores\components\RegistrationManager as RegistrationManager;

$ctrl = new RegistrationCtrl();
$ctrl->registerUser();

class RegistrationCtrl {

    /** @var JSONMessageSender */
    private $msg_sender;

    /** @var Request */
    private $request;

    public function __construct() {
        $this->msg_sender = new JSONMessageSender();
        $this->request = Request::createFromGlobals();
    }

    public function registerUser() {

        $error_obj = $this->validateInput();

        if ($error_obj->getType())
            $this->sendError($error_obj);

        try {

            $new_user = new CoreUserRegistrant($this->request);

            $handler = new RegistrationManager();

            if ($handler->checkUserName($new_user->uname)) {
                $error = new ValidationError();
                $error->setMsg("User name already exists");
                $error->setField("uname");
                $error->setCode(0);
                $error->setType(\VAL_FIELD_ERROR);

                $this->sendError($error);
            }

            //Check if the email is already in the system
            if ($handler->checkEmail($new_user->email)) {
                $error = new ValidationError();
                $error->setMsg("An account with this e-mail address already exists");
                $error->setField("email1");
                $error->setCode(0);
                $error->setType(\VAL_FIELD_ERROR);

                $this->sendError($error);
            }

            $handler->registerUser($new_user);
        } catch (Exception $e) {

            $error = new ValidationError();
            $error->setMsg($e->getMessage());
            $error->setCode($e->getCode());
            $error->setType(VAL_SYSTEM_ERROR);

            $this->sendError($error->toStdClass());
        }


        $this->msg_sender->onResult(null, null);
    }

    /**
     * Validates fields submitted from the registration form
     * @return ValidationError
     */
    private function validateInput() {

        $requiredFields = array("uname" => "Username is missing or invalid",
            "psw1" => "Password is missing or invalid",
            "psw2" => "Password is missing or invalid",
            "fname" => "First Name is missing or invalid",
            "lname" => "Last Name is missing or invalid",
            "phone" => "Phone is missing or invalid",
            "email1" => "Email is missing or invalid",
            "email2" => "Email is missing or invalid",
            "pi_name" => "PI name missing or invalid",
            "pi_email" => "PI e-mail missing or invalid",
            "pi_phone" => "PI phone missing or invalid",
            "pi_address_1" => "PI address missing or invalid",
            "pi_city" => "PI city missing or invalid",
            "pi_state" => "PI state missing or invalid",
            "pi_zip" => "PI zip code missing or invalid",
        );


        /* @var $error ValidationError */
        $error = new ValidationError();

        //check if required variables are defined
        foreach ($requiredFields as $key => $value) {
            if (empty($this->request->request->get($key, null))) {
                $error->setField($key);
                $error->setMsg($value);
                $error->setType(\VAL_FIELD_ERROR);

                return $error;
            }
        }

        //Validate username
        $uname = $this->request->request->get('uname');

        if (!\preg_match('/^[A-Za-z][A-Za-z0-9]{5,15}$/', $uname)) {
            $error->setField("uname");
            $error->setMsg("Username must be 6-16 chars long and start with a letter");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        $phone = $this->request->request->get('phone');

        if (!\preg_match('/^\(\d{3}\) \d{3}-\d{4}$/', $phone)) {
            $error->setField("phone");
            $error->setMsg("Phone number format should be (555) 555-5555");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        $fname = $this->request->request->get('fname');

        if (\strlen($fname) < 2) {
            $error->setField("fname");
            $error->setMsg("First name must be at least 2 characters long");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        $lname = $this->request->request->get('lname');

        if (\strlen($lname) < 2) {
            $error->setField("lname");
            $error->setMsg("Last name must be at least 2 characters long");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        $email_1 = $this->request->request->get('email1');
        $email_2 = $this->request->request->get('email2');


        //Check if emails match
        if (\strcmp($email_1, $email_2) != 0) {
            $error->setField("email2");
            $error->setMsg("E-mail addresses are different");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        /*
          Only check one email. If both emails are thesame, we only need to check one of them.
         */
        if (!\filter_var($email_1, FILTER_VALIDATE_EMAIL)) {
            $error->setField("email1");
            $error->setMsg("E-mail is invalid");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }


        $pass_1 = $this->request->request->get('psw1');
        $pass_2 = $this->request->request->get('psw2');

        //Check if emails match
        if (\strcmp($pass_1, $pass_2) != 0) {
            $error->setField("psw2");
            $error->setMsg("Passwords are different");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        /*
          Only check one password. If both passwords are thesame, we only need to check one of them.
         */
        if (!\preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/', $pass_1)) {
            $error->setField("psw1");
            $error->setMsg("Password must have a digit, lower and upper case charters. Min lenght is 6");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        $pi_name = $this->request->request->get('pi_name');

        if (\strlen($pi_name) < 3) {
            $error->setField("pi_name");
            $error->setMsg("PI name must be at least 3 characters long");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        $pi_email = $this->request->request->get('pi_email');

        if (!\filter_var($pi_email, FILTER_VALIDATE_EMAIL)) {
            $error->setField("pi_email");
            $error->setMsg("PI e-mail is invalid");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        $pi_phone = $this->request->request->get('pi_phone');

        if (!\preg_match('/^\(\d{3}\) \d{3}-\d{4}$/', $pi_phone)) {
            $error->setField("pi_phone");
            $error->setMsg("Phone number format should be (555) 555-5555");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        $pi_city = $this->request->request->get('pi_city');

        if (\strlen($pi_city) < 2) {
            $error->setField("pi_city");
            $error->setMsg("City must be at least 2 charcters long");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }

        $pi_state = $this->request->request->get('pi_state');

        if (!\preg_match('/^[A-Z]{2}$/', $pi_state)) {
            $error->setField("pi_state");
            $error->setMsg("Please select a valid state");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }


        $pi_zip = $this->request->request->get('pi_zip');

        if (!\preg_match('/^\d{5}$/', $pi_zip)) {
            $error->setField("pi_zip");
            $error->setMsg("Zip code must be 5 digits");
            $error->setType(\VAL_FIELD_ERROR);

            return $error;
        }


        return $error;
    }

    private function sendError(ValidationError $error) {
        $this->msg_sender->onError($error->toStdClass(), '', $error->getType());
    }

}
