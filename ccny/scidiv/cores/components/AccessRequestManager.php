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

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\permissions\PermissionManager as PermissionManager;
use ccny\scidiv\cores\model\CoreServiceDAO as CoreServiceDAO;
use ccny\scidiv\cores\model\CoreService as CoreService;
use ccny\scidiv\cores\permissions\ServicePermToken as ServicePermToken;

class AccessRequestManager extends CoreComponent
{
    //DB mysqli object
    private $mysqli;
    
    /* @var $perm_manager PermissionManager */
    private $perm_manager;
    

    public function __construct($mysqli = null) {
        parent::__construct();
        
        if (!is_null($mysqli)) {
            $this->mysqli = $mysqli;
        } else {
            $dbinfo = DbConnectInfo::getDBConnectInfoObject();

            @$this->mysqli = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

            if ($this->mysqli->connect_errno) {
                $this->throwDBError($this->mysqli->connect_error, $this->mysqli->connect_errno);
            }
        }
        
        $this->perm_manager = new PermissionManager($this->mysqli);
        
        
    }

    function requestServiceAccess(CoreUser $user,$service_id)
    {
        
        /* @var $service CoreService */
        $service = null;
        
        /* @var $sdao CoreServiceDAO */
        $sdao = new CoreServiceDAO($this->mysqli);
        
        try{
            $service = $sdao->getCoreService($service_id);
        } catch (Exception $ex) {
            $this->log(__CLASS__ . "::" . __FUNCTION__ . ": Could not access service info.", ERROR_LOG_TYPE);
        }
        
        if(  !$service instanceof CoreService)
        {
            $this->throwExceptionOnError("Service not found", 0, ERROR_LOG_TYPE);
        }
            
        $token = ServicePermToken::makeToken($user, $service);
        
        if (!$this->perm_manager->checkPermission(PERM_REQ_SERVICE_ACCESS, $token)) {
                $this->throwExceptionOnError("Insufficient user permissions", 0, SECURITY_LOG_TYPE);
        }
        
        $user_id = $user->getUserID();
        
        //If operation is allowed, add request to the database
        $insert_q = "INSERT INTO `core_access_request` (`id`, `user_id`, `service_id`, `date_requested`,`status`) VALUES (NULL, ?, ?, now(),0)";

        if (!$stmt = $this->mysqli->prepare($insert_q)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->bind_param('ii', $user_id, $service_id)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        $stmt->close();

        return 1;

    }

    
    public function hasRequestedAccess(CoreUser $user,$service_id)
    {

        $user_id = $user->getUserID();
        
        $has_requested = false;

        //---BEGIN: Get access requests. This array will store ids of services for which request has been submitted.
        $request_q = "SELECT `id` FROM `core_access_request` WHERE `status` = 0 AND `user_id` = ? AND `service_id` = ?";
      
        if (!$stmt = $this->mysqli->prepare($request_q)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->bind_param('ii', $user_id, $service_id)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        $stmt->store_result();

        if ($stmt->num_rows > 0 )
        {
            $has_requested = true;
        }

        $stmt->free_result();
        $stmt->close();

        return $has_requested;

    }

}

