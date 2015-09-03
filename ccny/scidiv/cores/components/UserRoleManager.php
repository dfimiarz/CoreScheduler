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
/**
 * Description of UserRoleManager
 *
 * @author WORK 1328
 */

include_once __DIR__ . '/../model/CoreUser.php';
include_once __DIR__ . '/../model/CoreRole.php';
include_once __DIR__ . '/../model/CoreEvent.php';
include_once __DIR__ . '/SystemConstants.php';

use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\CoreRole as CoreRole;
use ccny\scidiv\cores\model\CoreEvent as CoreEvent;

class UserRoleManager {
    
    static function getUserRolesForService(CoreUser $user, $service_id)
    {
        $active_roles = array();

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
        
        if( ! count($active_roles))
        {
            $active_roles[] = NO_ROLE;
        }
 
        return $active_roles;
        
    }
    
    static function getSystemRoles(CoreUser $user)
    {
        $roles = [];
        
        if ( $user->isAuth()) {
            $roles[] = SYS_ROLE_AUTHENTICATED;
        }
        else {
            $roles[] = SYS_ROLE_ANONYMOUS;
        }
        
        return $roles;
    }
    
    static function getEventRoles(CoreUser $user,CoreEvent $event )
    {
        $roles = [];
        
        if ($user->getUserID() == $event->getUserId()) {
            $roles[] = EVENT_ROLE_OWNER;
        }
        
        if( ! count($roles))
        {
            $roles[] = NO_ROLE;
        }
        
        return $roles;
    }
}
