<?php

namespace ccny\scidiv\cores\components;

include_once __DIR__ . '/CoreComponent.php';
include_once __DIR__ . '/DbConnectInfo.php';

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\model\CoreUserRegistrant as CoreUserRegistrant;

class RegistrationManager extends CoreComponent
{

    /** @var mysqli */
    private $mysqli;

    //Class constructor
    function __construct() {

        parent::__construct();

        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        @$this->mysqli = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($this->mysqli->connect_errno) {
            $this->throwDBError($this->mysqli->connect_error, $this->mysqli->connect_errno);
        }
    }

    function __destruct() {

        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }

    public function registerUser(CoreUserRegistrant $new_user) {

        /*
          ---MYSQL STORED PROCEDURE DETAILS---

          add_new_user(IN first_name VARCHAR(64),IN last_name VARCHAR(64),IN phone VARCHAR(32),IN email VARCHAR(128),IN username VARCHAR(16),IN pass VARCHAR(45),IN pi_name VARCHAR(64), IN  pi_email VARCHAR(128) , IN pi_phone VARCHAR(16), IN  pi_address1 VARCHAR(128), IN pi_address2 VARCHAR(128), IN pi_city VARCHAR(32), IN  pi_state VARCHAR(2), IN pi_zip VARCHAR(5))
         */

        $insert_proc_call = "CALL add_new_user(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        if (!($stmt = $this->mysqli->prepare($insert_proc_call))) {
            $this->throwDBExceptionOnError($this->mysqli->errno, $this->mysqli->error);
        }

        if (!($stmt->bind_param("ssssssssssssss", $new_user->fname, $new_user->lname, $new_user->phone, $new_user->email, $new_user->uname, $new_user->psw, $new_user->pi_name, $new_user->pi_email, $new_user->pi_phone, $new_user->pi_address_1, $new_user->pi_address_2, $new_user->pi_city, $new_user->pi_state, $new_user->pi_zip))) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!($stmt->execute())) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        $stmt->close();

        $this->log(__FUNCTION__ . ": Registration complete", \ACTIVITY_LOG_TYPE);

        return 1;
    }

    public function checkUserName($username) {
        /*
          This function checks if a given user name alrady exisits.
          Returns TRUE if it does, FALSE if it does not exist.
         */

        $query = "SELECT * FROM core_users WHERE username = ?";

        if (!($stmt = $this->mysqli->prepare($query))) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!($stmt->bind_param("s", $username))) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!($stmt->execute())) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!($stmt->store_result())) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        $user_count = $stmt->num_rows;

        $stmt->close();

        if ($user_count == 0) {
            $this->log(__FUNCTION__ . ": Username not available.", \ACTIVITY_LOG_TYPE);
            return false;
        }

        return true;
    }

    public function checkEmail($email) {
        /*
          This function checks if a given user name alrady exisits.
          Returns TRUE if it does, FALSE if it does not exist.
         */

        $query = "SELECT * FROM core_users WHERE email = ?";

        if (!($stmt = $this->mysqli->prepare($query))) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!($stmt->bind_param("s", $email))) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!($stmt->execute())) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!($stmt->store_result())) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        $user_count = $stmt->num_rows;

        $stmt->close();

        if ($user_count == 0) {
            $this->log(__FUNCTION__ . ": E-mail already in use.", \ACTIVITY_LOG_TYPE);
            return false;
        }

        return true;
    }

}
