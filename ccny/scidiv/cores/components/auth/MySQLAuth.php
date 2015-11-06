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

namespace ccny\scidiv\cores\components\auth;

use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\components\auth\UserAuth as UserAuth;


class MySQLAuth extends UserAuth {

    function __construct() {

        parent::__construct();
   
    }

    function authenticate($username, $password) {

        $authenticated = false;
        
        $dbinfo = DbConnectInfo::getDBConnectInfoObject();
        
        @$mysqli = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($mysqli->connect_errno) {
            $this->throwDBError($mysqli->connect_error, $mysqli->connect_errno);
        }
        
         //Find if there is a username/password matching the input
        $auth_q = "SELECT u.id FROM core_users u WHERE u.username = ? AND u.password = SHA( ? ) limit 1";
        if (!$stmt = $mysqli->prepare($auth_q)) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        if (!$stmt->bind_param('ss', $username, $password)) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        //store the result in memory
        if (!$stmt->store_result()) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        //get number of rows returned
        $rows = $stmt->num_rows;

        if ($rows == 1) {
            $authenticated = true;
                  
        }
        else {
            $authenticated = false;
            
        }

        $stmt->free_result();
        $stmt->close();
        $mysqli->close();

        return $authenticated;
    }

}
