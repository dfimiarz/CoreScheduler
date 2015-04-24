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

include_once '/../components/SystemConstants.php';
include_once '/../components/CoreComponent.php';

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;

class PermissionManager extends CoreComponent {

   
    //DB mysqli object
    private $mysqli;
    
    // A private constructor; prevents direct creation of object
    public function __construct($mysqli) {
        parent::__construct();
        $this->mysqli = $mysqli;
    }

    //This function get permissions for ROLES and SERVICE_ID 
    public function getPermissions($roles_array, $service_id) {

        $permissions = array();

        if (!is_array($roles_array)) {
            $this->throwExceptionOnError("Error verifying user roles", 0, \ERROR_LOG_TYPE);
        }


        //Get all user roles and permissions for a given state
        $permission_q = "SELECT cpc.role_id,cpc.perm_id FROM core_permission_config cpc, core_services cs where cpc.service_state_id = cs.state and cs.id = ?";

        if (!$stmt = $this->mysqli->prepare($permission_q)) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!$stmt->bind_param('i', $service_id)) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        $perm_id = 0;
        $role_id = 0;

        if (!$stmt->execute()) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!$stmt->store_result()) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        $stmt->bind_result($role_id, $perm_id);

        while ($stmt->fetch()) {

            if (in_array($role_id, $roles_array)) {
                if (!in_array($perm_id, $permissions)) {
                    $permissions[] = $perm_id;
                }
            }
        }

        $stmt->free_result();
        $stmt->close();

        return $permissions;
    }

    public function hasPermission($permissions_array, $permission) {

        if (!is_array($permissions_array)) {
            return false;
        }

        if (in_array($permission, $permissions_array)) {
            return true;
        }

        return false;
    }

}
