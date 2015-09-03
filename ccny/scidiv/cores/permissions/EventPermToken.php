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

namespace ccny\scidiv\cores\permissions;

use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\CoreEvent as CoreEvent;
use ccny\scidiv\cores\components\UserRoleManager as UserRoleManager;

/**
 * Description of EventPermToken
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class EventPermToken extends PermissionToken {
    
    public function __construct($system_roles,$event_roles,$service_roles,$service_states,$time_states) {
        
        parent::__construct();
        $this->setAttribute("system_roles", $system_roles);
        $this->setAttribute("event_roles", $event_roles);
        $this->setAttribute("service_roles", $service_roles);
        $this->setAttribute("service_states", $service_states);
        $this->setAttribute("time_states", $time_states);
        
    }
    
    static function makeToken(CoreUser $user,CoreEvent $event)
    {
        $service_roles = UserRoleManager::getUserRolesForService($user, $event->getServiceId());
        $event_roles = UserRoleManager::getEventRoles($user, $event);
        $system_roles = UserRoleManager::getSystemRoles($user);
        return new EventPermToken($system_roles,$event_roles,$service_roles,$event->getServiceState(),$event->getTemporalState());
    }
    
}

