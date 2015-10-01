<?php

/*
 * The MIT License
 *
 * Copyright 2015 Daniel Fimiarz <dfimiarz@ccny.cuny.edu>.
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

namespace ccny\scidiv\cores\components;

include_once __DIR__ . '/SystemConstants.php';

use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\components\CryptoManager as CryptoManager;
use ccny\scidiv\cores\components\CoreComponent as CoreComponent;

class UserManager extends CoreComponent {

    /** @var \mysqli */
    private $mysqli;
    /** @var CryptoManager */
    private $crypto;

    function __construct() {

        parent::__construct();
        
        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        @$this->mysqli = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($this->mysqli->connect_errno) {
            $this->throwDBError($this->mysqli->connect_error,$this->mysqli->connect_errno);
        }

        $this->crypto = new CryptoManager();
    }

    function __destruct() {

        if ($this->mysqli)
            $this->mysqli->close();
    }

    /*
    public function getAuthorizedUsers($resource_id = null, $service_id = null) {
        $users = array();

        //prepare encryption functionality
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        if (!isset($_SESSION['user_id']))
            $this->throwCustomExceptionOnError(0, 'Please log in.');

        if ($resource_id == null && $service_id == null)
            return $users;

        $q_modifier = "";
        $param = 0;

        if ($resource_id != null) {
            $q_modifier = " AND service_id IN (SELECT id FROM core_services where resource_id = ?)";
            $param = $resource_id;
        }

        if ($service_id != null) {
            $q_modifier = " AND service_id = ?";
            $param = $service_id;
        }

        $logged_in_user_id = $_SESSION['user_id'];

        $get_users_q = "SELECT u.id,u.username,concat(u.firstname,' ',u.lastname) as fullname,u.email,u.phone,concat(p.first_name,' ',p.last_name) as pi_name FROM core_permissions cp,core_users u,people p where role = 2 and service_id in (select service_id from core_permissions where role = 3 and user_id = ? {M}) and cp.user_id = u.id and u.pi = p.individual_id";

        $get_users_q = str_replace("{M}", $q_modifier, $get_users_q);


        if (!($stmt = $this->mysqli->prepare($get_users_q))) {
            $this->throwDBExceptionOnError($this->mysqli->errno, $this->mysqli->error);
        }

        if (!($stmt->bind_param("ii", $logged_in_user_id, $param))) {
            $this->throwDBExceptionOnError($stmt->errno, $stmt->error);
        }

        if (!($stmt->execute())) {
            $this->throwDBExceptionOnError($stmt->errno, $stmt->error);
        }

        $temp = new stdClass();

        if (!($stmt->bind_result($temp->id, $temp->username, $temp->fullname, $temp->email, $temp->phone, $temp->piname))) {
            $this->throwDBExceptionOnError($stmt->errno, $stmt->error);
        }

        while ($stmt->fetch()) {

            $user = new stdClass();
            $user->id = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $temp->id, MCRYPT_MODE_ECB, $iv));
            $user->username = $temp->username;
            $user->name = $temp->fullname;
            $user->email = $temp->email;
            $user->phone = $temp->phone;
            $user->pi = $temp->piname;

            $users[] = $user;
        }

        $stmt->close();

        return $users;
    }

    public function getUserDetails($enc_person_id = null) {
        $logged_in_user_id = null;
        $person_id = null;

        //Get the logged in id and person id that was sent from the client
        if (!isset($_SESSION['user_id']))
            $this->throwCustomExceptionOnError(0, 'Please log in.');
        else
            $logged_in_user_id = $_SESSION['user_id'];

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $person_id = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($enc_person_id), MCRYPT_MODE_ECB, $iv));

        //Run a query to get the info for the user

        $person_details = new stdClass();

        $person_details->uname = 'jdoe';
        $person_details->fname = 'John Doe';
        $person_details->phone = '(555) 555-5555';
        $person_details->email = 'jdoe@gmail.com';
        $person_details->piname = 'John Doe';
        $person_details->piphone = '(555) 555-5555';
        $person_details->piemail = 'jdoe@gamil.com';
        $person_details->pi_addr_l1 = '123 45 st. #6';
        $person_details->pi_addr_l2 = 'New Town, New Florida, 12345';
        $person_details->pi_addr_l3 = '';


        return $person_details;
    }
    */

    public function initPasswordReset($user_name) {

        $hash_data = "aErl67g&" . $user_name . date("D M j G:i:s T Y");
        $ver_code = hash("sha256", $hash_data, false);

        $query = "CALL init_pass_reset(?,?)";

        if (!($stmt = $this->mysqli->prepare($query))) {
            $this->throwDBError( $this->mysqli->error, $this->mysqli->errno);
        }

        if (!($stmt->bind_param("ss", $user_name, $ver_code))) {
            $this->throwDBError($stmt->error,$stmt->errno);
        }

        if (!($stmt->execute())) {
            $this->throwDBError($stmt->error,$stmt->errno);
        }

        $stmt->close();

        $this->log(__FUNCTION__ . ": Password reset initiated.", \ACTIVITY_LOG_TYPE);

        return 1;
    }

    public function doPasswordReset($ver_code, $new_pass) {

        $query = "CALL do_pass_reset(?,?)";

        if (!($stmt = $this->mysqli->prepare($query))) {
           $this->throwDBError( $this->mysqli->error, $this->mysqli->errno);
        }

        if (!($stmt->bind_param("ss", $ver_code, $new_pass))) {
            $this->throwDBError($stmt->error,$stmt->errno);
        }

        if (!($stmt->execute())) {
            $this->throwDBError($stmt->error,$stmt->errno);
        }

        $stmt->close();

        $this->log(__FUNCTION__ . ": Password reset.", \ACTIVITY_LOG_TYPE);

        return 1;
    }

    public function doLoginReminder($email) {

        $query = "CALL do_login_reminder(?)";

        if (!($stmt = $this->mysqli->prepare($query))) {
            $this->throwDBError( $this->mysqli->error, $this->mysqli->errno);
        }

        if (!($stmt->bind_param("s", $email))) {
            $this->throwDBError($stmt->error,$stmt->errno);
        }

        if (!($stmt->execute())) {
            $this->throwDBError($stmt->error,$stmt->errno);
        }

        $stmt->close();

        $this->log(__FUNCTION__ . ": Username reminder recorded for processing.",\ACTIVITY_LOG_TYPE);

        return 1;
    }

}
