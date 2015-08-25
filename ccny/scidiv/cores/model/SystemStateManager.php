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

namespace ccny\scidiv\cores\model;

include_once __DIR__ . '/../components/DbConnectInfo.php';
include_once __DIR__ . '/../components/SystemConstants.php';
include_once __DIR__ . '/../components/CoreComponent.php';
include_once __DIR__ . '/../components/UserRoleManager.php';
include_once __DIR__ . '/../components/AccessRequestManager.php';
include_once __DIR__ . '/PermissionManager.php';
include_once __DIR__ . '/CoreUser.php';

use ccny\scidiv\cores\components\CoreComponent as CoreComponent; 
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\model\PermissionManager as PermissionManager;
use ccny\scidiv\cores\components\UserRoleManager as UserRoleManager;
use ccny\scidiv\cores\components\AccessRequestManager as AccessRequestManager;
use ccny\scidiv\cores\model\CoreUser as CoreUser;


class SystemStateManager extends CoreComponent
{

    private $mysqli;
    
    /* @var $pm_handler PermissionManager */
    private $pm_handler;
   
    /* @var $ar_handler AccessRequestManager */
    private $ar_handler;

    //Class constructor
    function __construct() {

        parent::__construct();
        
        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        $this->mysqli = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($this->mysqli->connect_errno) {
            $this->throwDBError($this->mysqli->connect_error, $this->mysqli->connect_errno);
        }

        $this->pm_handler = new PermissionManager($this->mysqli);
        $this->ar_handler = new AccessRequestManager($this->mysqli);
        
    }

    function __destruct() {

        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }

    public function getUserSessionConfig($service_id,  CoreUser $user) {

        //Get the following info for user and service
        //Default
        $session_info = new \stdClass();

        //Text to display
        $session_info->txt = '';
        $session_info->can_add = 0;
        $session_info->show_txt = 0;
        $session_info->show_req_btn = 0;

        $user_roles = UserRoleManager::getUserRolesForService($user, $service_id, false);
        $permissions_a = $this->pm_handler->getPermissions($user_roles, $service_id);

        if ($this->pm_handler->hasPermission($permissions_a, \PERM_CREATE_EVENT)) {
            $session_info->can_add = 1;
        }
        
        if ($this->pm_handler->hasPermission($permissions_a, \PERM_REQUEST_ACCESS)) {
            $session_info->show_req_btn = 1;
        }

        $all_user_roles = $user->getRoles();

        //If user has a role assigned, show it
        if (array_key_exists($service_id, $all_user_roles)) {
            $session_info->show_txt = 1;
            
            $role_name = 'Unknown';
            
            $role = $all_user_roles[$service_id];
            if( $role instanceof CoreRole)
            {
                $role_name = $role->getRoleName();
            }
            
            $session_info->txt = 'Access Level: ' . $role_name;
            $session_info->show_req_btn = 0;

            return $session_info;
        }
        
        $has_req_access = $this->ar_handler->hasRequestedAccess($user, $service_id);

        if ($has_req_access) {
            $session_info->txt = 'Pending approval...';
            $session_info->show_txt = 1;
            $session_info->show_req_btn = 0;
            
            return $session_info;
        }

        return $session_info;
    }

}
