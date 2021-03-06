<?php

/*
 * The MIT License
 *
 * Copyright 2015 Daniel F.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace ccny\scidiv\cores\ctrl;

include_once __DIR__ . '/../../../../vendor/autoload.php';

use ccny\scidiv\cores\model\LoginManager as LoginManager;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\ctrl\RAPController as RAPController;
use ccny\scidiv\cores\components\SystemException as SystemException;

/**
 * Allows for a login through a POST request (using the Redirect After Post
 * methodology). Logging probably does not deserve ajax due lesser impact on
 * user experience. 
 *
 * @author Daniel F
 */
class LoginCtrl extends RAPController {

    public function __construct() {

        parent::__construct();
    }

    public function run() {
        /*
         * Clear session variables and regenerate ID
         */
        $this->session->invalidate();

        /*
         * Get username and password from the $_POST
         */
        $username = $this->request->request->get("user", null);
        $password = $this->request->request->get("pass", null);
        /*
         * If username or password is empty, throw error
         */
        if ( empty($username) || empty($password)) {
            $this->failure("User name or password cannot be empty");
        }

        $user = new CoreUser($username);

        $login_manager = new LoginManager($user);
        $login_manager->authenticateUser($password);
       
        if (!$user->isAuth()) {
            $this->failure("Login failed. Please check your password and try again.");
        }

        try {
            if (!$login_manager->getAccountInfo()) {
                $this->failure("Could not load user account");
            }
        } catch (SystemException $e) {
            $client_error = $e->getUIMsg();

            if (empty($client_error)) {
                $client_error = "Operation failed: Error code " . $e->getCode();
            }

            $this->failure($client_error);
        } catch (\Exception $e) {
            $err_msg = "Unexpected error:  " . $e->getCode();
            $this->failure($err_msg);
        }

        /*
         * User authenticated. Save user object in the session.
         */
        $this->session->set('coreuser', $user);

        $this->success();
    }

    protected function success() {
        /*
         * No errors, redirect to dest
         */
        $this->redirect();
    }

    protected function failure($error_msg) {
        
        /*
         * Upon failure, send user back to the login form
         */
        $this->dest_page = 'login';
        
        /*
         * If the $_POST['dest'] was set, pass it back to the form as a $_GET
         * parameter
         */
        $dest_param = $this->request->request->get("dest", null);
        if (!is_null($dest_param)) {
            $this->dest_params = "?dest" . \urlencode($dest_param);
        }
        
        /*
         * Set the error message for the form
         */
        $this->session->set('login_err', $error_msg);

        /*
         * Redirect to $this->dest_code
         */
        $this->redirect();
        
        
    }

}

$ctrl = new LoginCtrl();
$ctrl->run();

