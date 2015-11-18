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

        if ($this->mysqli) {
            $this->mysqli->close();
        }
            
    }

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
