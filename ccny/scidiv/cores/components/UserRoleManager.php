<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace ccny\scidiv\cores\components;
/**
 * Description of UserRoleManager
 *
 * @author WORK 1328
 */

include_once __DIR__ . '/../model/CoreUser.php';
include_once __DIR__ . '/../model/CoreRole.php';
include_once __DIR__ . '/SystemConstants.php';

use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\CoreRole as CoreRole;

class UserRoleManager {
    
    static function getUserRolesForService(CoreUser $user, $service_id, $is_owner)
    {
        $active_roles = array();

        //Set up special roles first
        if ( $user->isAuth()) {
            $active_roles[] = \DB_ROLE_AUTHENTICATED;
        } else {
            $active_roles[] = \DB_ROLE_ANONYMOUS;
        }

        if ($is_owner) {
            $active_roles[] = \DB_ROLE_OWNER;
        }

        //Get roles assigned at login time from the database
        $db_roles = $user->getRoles();
        
        if( isset($db_roles[$service_id]))
        {
            $role = $db_roles[$service_id];
            
            if( $role instanceof CoreRole)
            {
                $active_roles[] = $role->getRoleId();
            }
            
        }

        return $active_roles;
        
    }
}
