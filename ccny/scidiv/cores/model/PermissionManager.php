<?php

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
