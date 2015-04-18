<?php

namespace ccny\scidiv\cores\components\auth;

include_once __DIR__ . '/../SystemConstants.php';
include_once __DIR__ . '/../DbConnectInfo.php';

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
