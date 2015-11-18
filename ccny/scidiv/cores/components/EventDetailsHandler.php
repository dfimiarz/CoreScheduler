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
use ccny\scidiv\cores\components\UserRoleManager as UserRoleManager;
use ccny\scidiv\cores\components\CryptoManager as CryptoManager;
use ccny\scidiv\cores\model\CoreEventDetails as CoreEventDetails;
use ccny\scidiv\cores\model\CoreEventDetailsDAO as CoreEventDetailsDAO;
use ccny\scidiv\cores\permissions\PermissionManager as PermissionManager;
use ccny\scidiv\cores\permissions\EventPermToken as EventPermToken;

/**
 * Description of EventDetailsHandler
 *
 * @author WORK 1328
 */
class EventDetailsHandler extends CoreComponent {

    //put your code here
    private $pm;
    
    private $connection;

    private $detailsDAO;
    
     /* @var $crypto CryptoManager */
    private $crypto;
    
    /* @var $crypto CoreUser */
    private $user;

    public function __construct(CoreUser $core_user) {

        parent::__construct();

        $this->user = $core_user;

        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        @$this->connection = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($this->connection->connect_errno) {
            $this->throwDBError($this->connection->connect_error, $this->connection->connect_errno);
        }

        $this->pm = new PermissionManager($this->connection);
        $this->detailsDAO = new CoreEventDetailsDAO($this->connection);
        
        $this->crypto = new CryptoManager();
    }

    /**
     * getEventDetails()
     * 
     * Returns array of values that can be then passed to the templating engine for rendering
     * 
     * @param type $encrypted_record_id
     * @return Array
     */
    public function getEventDetails(\stdClass $params) {

        
        $encrypted_record_id = (isset($params->encrypted_event_id) ? $params->encrypted_event_id : null);
        /* @var $timestamp \DateTime */
        $timestamp = (isset($params->timestamp) ? $params->timestamp : null);

        $now_dt = new \DateTime();

        $record_id = $this->crypto->decrypt($encrypted_record_id);
                
        /* @var $details CoreEventDetails */
        $details = $this->detailsDAO->getCoreEventDetails($record_id,new \DateTime($timestamp));
        
        if( ! $details instanceof CoreEventDetails)
        {
            $sys_err_msg = __FUNCTION__ . ": EVENT NOT FOUND. USER: " . $this->user->getUserName();
            $err_ino = new ErrorInfo($sys_err_msg, 0, "Event not found or already modified", ERROR_LOG_TYPE);   
            $this->throwExceptionOnError ($err_ino);
        }
        
        $start_dt = $details->getStart();
        $end_dt = $details->getEnd();
        

        $token = EventPermToken::makeToken($this->user, $details);

        $ArrDetails = array();
        
        $ArrDetails['username'] = $details->getUsername();
        /*
         * Compare dates to decide on the format of the time
         */
        $start_d = $start_dt->format("m/d/y");
        $end_d = $end_dt->format("m/d/y");
        
        if (\strcmp($start_d, $end_d) == 0) {
            $ArrDetails['time'] = $start_dt->format("M j, Y g:ia") . " - " . $end_dt->format("g:ia");
        } else {
            $ArrDetails['time'] = $start_dt->format("M j, Y g:ia") . " - " . $end_dt->format("M j, Y g:ia");
        }

        $ArrDetails['activity'] = $details->getService() . ', ' . $details->getResource();
        /* @var $timestamp_dt \DateTime */
        $timestamp_dt = $details->getTimestamp();
        $ArrDetails['timestamp'] = $timestamp_dt->format('Y-m-d H:i:s');


        if ($this->pm->checkPermission(PERM_VIEW_DETAILS, $token)) {

            $ArrDetails['record_id'] = $encrypted_record_id;

            /*
             * Show full name and username with DB_PERM_VIEW_DETAILS
             */

            $ArrDetails['email'] = $details->getEmail();
            $ArrDetails['pi'] = $details->getPiname();
            $ArrDetails['note'] = $details->getNote();
        }

        if ($this->pm->checkPermission(PERM_DELETE_EVENT, $token)) {
            $ArrDetails['can_cancel'] = true;
        }

        if ($this->pm->checkPermission(PERM_CHANGE_OWNER, $token)) {
            $user_id_enc = $this->crypto->encrypt($details->getUserId());
            $ArrDetails['user_id'] = $user_id_enc;
            $ArrDetails['can_edit_user'] = true;
        }


        //Does user have DB_PERM_CHANGE_NOTE
        if ($this->pm->checkPermission(PERM_CHANGE_NOTE, $token)) {
            $ArrDetails['can_edit_note'] = true;
        }

        return $ArrDetails;
    }

}
