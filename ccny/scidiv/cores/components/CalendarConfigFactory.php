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

use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\CalendarConfig as CalendarConfig;
use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\model\CoreServiceDAO as CoreServiceDAO;
use ccny\scidiv\cores\permissions\ServicePermToken as ServicePermToken;
use ccny\scidiv\cores\permissions\PermissionManager as PermissionManager;
use ccny\scidiv\cores\model\CoreService as CoreService;
use ccny\scidiv\cores\components\AccessRequestManager as ARM;

/**
 * Description of CalendarConfigGetter
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class CalendarConfigFactory extends CoreComponent {
    //put your code here
    
    /* @var $user CoreUser */
    private $user;
    private $service_id;
    private $mysqli;
    private $permManager;
    /* @var $arm AccessRequestManager */
    private $arm;
    
    public function __construct(CoreUser $user,$service_id) {
        
        $this->user = $user;
        $this->service_id = $service_id;
        
        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        @$this->mysqli = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($this->mysqli->connect_errno) {
            $this->throwDBError($this->mysqli->connect_error, $this->mysqli->connect_errno);
        }
        
        $this->permManager = new PermissionManager($this->mysqli);
        $this->arm = new AccessRequestManager($this->mysqli);
    }
    
    public function getCalendarConifg()
    {
        /* @var $service CoreService */
        $service = NULL;
        
        $config = new CalendarConfig();
                
        /* @var $sdao CoreServiceDAO */
        $sdao = new CoreServiceDAO($this->mysqli);
        
        try{
            $service = $sdao->getCoreService($this->service_id);
        } catch (Exception $ex) {
            return $config;
        }
        
        if(  !$service instanceof CoreService)
        {
            return $config;
        }
            
        $token = ServicePermToken::makeToken($this->user, $service);
             
        if ($this->permManager->checkPermission(PERM_ACCESS_SERVICE, $token)) {

            $config->can_use = TRUE;
            $config->can_request_access = FALSE;

            $all_user_roles = $this->user->getRoles();

            $role_name = 'Unknown';

            //If user has a role assigned, show it
            if (array_key_exists($this->service_id, $all_user_roles)) {

                $role = $all_user_roles[$this->service_id];
                if ($role instanceof CoreRole) {
                    $role_name = $role->getRoleName();
                }
            }

            $config->message = 'Access Level: ' . $role_name;
            
        } else {
            
            
            if ($this->permManager->checkPermission(PERM_REQ_SERVICE_ACCESS, $token)) {
                $config->can_request_access = TRUE;
                $config->can_use = FALSE;
            }
            
            if( $this->arm->hasRequestedAccess($this->user, $service->getId()))
            {
                $config->can_request_access = TRUE;
                $config->can_use = FALSE;
            }
            
            $config->message = 'Access requested';
        }
        
        return $config;
        
    }
}
