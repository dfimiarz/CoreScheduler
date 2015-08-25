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
include_once __DIR__ . '/../components/ColorSelector.php';
include_once __DIR__ . '/../components/CoreComponent.php';
include_once __DIR__ . '/../components/SystemConstants.php';
include_once __DIR__ . '/../components/UserRoleManager.php';
include_once __DIR__ . '/PermissionManager.php';
include_once __DIR__ . '/CoreUser.php';
include_once __DIR__ . '/CoreEvent.php';

use ccny\scidiv\cores\components\ColorSelector as ColorSelector;
use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\components\CryptoManager as CryptoManager;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\PermissionManager as PermissionManager;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\components\UserRoleManager as UserRoleManager;
use ccny\scidiv\cores\model\CoreEvent as CoreEvent;
use ccny\scidiv\cores\model\CoreEventDAO as CoreEventDAO;
use ccny\scidiv\cores\model\CoreEventHTTPParams as CoreEventHTTPParams;

class ScheduleDataHandler extends CoreComponent {

    /* @var $user CoreUser */
    private $user;
    
    private $connection;
    private $color_selector;
   
    private $permission_manager;
    
    /* @var $coreEventDAO Used to access database tables */
    private $coreEventDAO;
    
    /* @var $crypto CryptoManager */
    private $crypto;

    //Class constructor
    public function __construct(CoreUser $core_user) {

        parent::__construct();
        
        $this->user = $core_user;
        
        $this->color_selector = ColorSelector::getColorSelectorObject();

        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        @$this->connection = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($this->connection->connect_errno) {
            $this->throwDBError($this->connection->connect_error, $this->connection->connect_errno);
        }

        $this->permission_manager = new PermissionManager($this->connection);
        $this->coreEventDAO = new CoreEventDAO($this->connection);
        $this->crypto = new CryptoManager();
    }

    function __destruct() {

        mysqli_close($this->connection);
    }

    function createEvent($event_options) {
        
        /**
         * Create a new CoreEvent object with id = null and current time as timestamp. 
         * Event id set to null signifies a new event.
         */
        $new_event = new CoreEvent(null,new \DateTime());
        
        $start_dt = new \DateTime();
        $start_dt->setTimestamp($event_options->start);
        $new_event->setStart($start_dt);
        
        $end_dt = new \DateTime();
        $end_dt->setTimestamp($event_options->end);
        $new_event->setEnd($end_dt);
        
        $new_event->setServiceId($event_options->service_id);
        $new_event->setEventState(1);
        $new_event->setUserId($this->user->getUserID());
        
        $duration = $new_event->getDuration();

        /**
         * If the event is of a relativly short duration find another event
         * that can be extended instead.
         */
        $adj_event = null;
        
        if ($duration > 0 && $duration <= \MIN_EVENT_DURATION * 2) {
            $adj_event = $this->coreEventDAO->getAdjacentEvent($new_event);   
        }
        
        /*
         * If we find an adjacent event we should merge,otherwise  just add
         */
        if (!is_null($adj_event) && $adj_event instanceof CoreEvent) {
            $this->mergeCoreEvents($new_event, $adj_event);
        } else {
            $this->addNewEvent($new_event);
        }
    }

    private function addNewEvent(CoreEvent $new_event)
    {
        $now = new \DateTime();
        
        $user_roles = UserRoleManager::getUserRolesForService($this->user, $new_event->getServiceId(),$new_event->isOwner($this->user->getUserID()));
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $new_event->getServiceId());

        if (!$this->permission_manager->hasPermission($permissions_a, \PERM_CREATE_EVENT)) {
            $this->throwExceptionOnError ("Insufficient user permissions", 0, \SECURITY_LOG_TYPE);
        }

        if ($new_event->getStart() < $now) {
            if (!$this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("Adding a session in the past not allowed", 0, \SECURITY_LOG_TYPE);
            }
        }

        $new_event_id = $this->coreEventDAO->insertCoreEvent($new_event);

        $log_text = "Source: " . __CLASS__ . "::" . __FUNCTION__ . " Event added. ID: " . $new_event_id;
        
        $this->log($log_text, \ACTIVITY_LOG_TYPE);
    }

    function getEventsByEq($event_options) {

        /* @var $user CoreUser */
        $user = new CoreUser('anonymous');
        
        if( isset($event_options->user))
        {
            $user = $event_options->user;
            
            if( ! $user instanceof CoreUser)
            {
                $user = new CoreUser('anonymous');
            }
        }
        
        $temp_event_array = array();

        $logged_in_user_id = $user->getUserID();
        $is_owner = false;

        

        $now_dt = new \DateTime();

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $result_array = array();

        $start = new \DateTime();
        $start->setTimestamp($event_options->start);

        $end = new \DateTime();
        $end->setTimestamp($event_options->end);

        $eq_id = $event_options->eq_id;

        //Get all sessions for given service
        $query = "SELECT cta.id,cta.time_modified,cta.service_id,cs.short_name,cu.id,cu.username,cta.start,cta.end,cta.note,cs.state FROM core_timed_activity cta, core_users cu,core_services cs WHERE cta.start <= ? AND cta.end >= ? AND cta.service_id IN (SELECT id from core_services WHERE resource_id = ? ) AND cta.state = 1 AND cu.id = cta.user AND cs.id = cta.service_id";

        if (!$stmt = $this->connection->prepare($query)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $start_time_str = $start->format('Y-m-d H:i:s');
        $end_time_str = $end->format('Y-m-d H:i:s');

        if (!$stmt->bind_param('ssi', $end_time_str, $start_time_str, $eq_id)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();

        $stmt->bind_result($temp->id, $temp->timestamp,$temp->service_id,$temp->short_name, $temp->user_id, $temp->username, $temp->start, $temp->end, $temp->note, $temp->service_state);
        
        while ($stmt->fetch()) {

            $event_object = new \stdClass();
            $event_object->id = $temp->id;
            $event_object->service_id = $temp->service_id;
            $event_object->short_name = $temp->short_name;
            $event_object->user_id = $temp->user_id;
            $event_object->username = $temp->username;
            $event_object->start = $temp->start;
            $event_object->end = $temp->end;
            $event_object->note = $temp->note;
            $event_object->service_state = $temp->service_state;
            $event_object->timestamp = $temp->timestamp;

            $temp_event_array[] = $event_object;
        }

        $stmt->close();

        //Go through each session and determine what is show based on permissions
        foreach ($temp_event_array as $temp_event) {
            //Reset owner flag
            $is_owner = false;

            //Check if logged in user is owner of the event
            if ($logged_in_user_id == $temp_event->user_id) {
                $is_owner = true;
            }

            //$user_roles = $this->login_manager->getUserRoles($temp_event->service_id, $is_owner);
            $user_roles = UserRoleManager::getUserRolesForService($user, $temp_event->service_id, $is_owner);
            $permissions_a = $this->permission_manager->getPermissions($user_roles, $temp_event->service_id);

            $t_start = new \DateTime($temp_event->start);
            $t_end = new \DateTime($temp_event->end);

            $event = new \stdClass();
            $event->id = $this->crypto->encrypt($temp_event->id);

            $event->title = $temp_event->username;

            $event->description = $temp_event->short_name;
            $event->start = $t_start->format("Y-m-d\TH:i:s\Z");
            $event->end = $t_end->format("Y-m-d\TH:i:s\Z");
            $event->timestamp = $temp_event->timestamp;

            $event->allDay = false;

            $now_dt = new \DateTime();

            if ($t_start >= $now_dt) {
                $colors = $this->color_selector->getFutureColor($temp_event->service_id);
            } else {
                $colors = $this->color_selector->getPastColor($temp_event->service_id);
            }

            $event->color = $colors->bg;
            $event->textColor = $colors->txt;

            $event->editable = false;


            //Do not call function here for better speed
            if ($this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_EVENT)) {
                $event->editable = true;
            }

            if ($t_start < $now_dt) {
                if ($this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_PAST_EVENT)) {
                    $event->editable = true;
                } else {
                    $event->editable = false;
                }
            }

            //Determine visibility of an event
            if ($this->permission_manager->hasPermission($permissions_a, \PERM_VIEW_EVENT)) {
                $result_array[] = $event;
            }
        }

        return $result_array;
    }

    public function moveEvent(CoreEventHTTPParams $params) {
        
        $dayDelta = $params->getDayDelta();
        $minuteDelta = $params->getMinuteDelta();
        
        $logged_in_user_id = $this->user->getUserID();
                
        $dec_record_id = $this->crypto->decrypt($params->getEncRecID());
        
       /* @var $event CoreEvent */
        $event = $this->coreEventDAO->getCoreEvent($dec_record_id, $params->getTimestamp());
        
        if(! $event instanceof CoreEvent )
        {
            $this->throwExceptionOnError ("Event not found or already modified", 0, \ERROR_LOG_TYPE);
        }

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $event->getServiceId(), $event->isOwner($logged_in_user_id));
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $event->getServiceId());

        if (!$this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_EVENT)) {
            $this->throwExceptionOnError ("Insufficient user permissions", 0, \SECURITY_LOG_TYPE);
        }

        //Only DB_ADMIN can modify past session
        $now_dt = new \DateTime();

        $new_start_dt = $event->getStart();
        $new_end_dt = $event->getEnd();

        $days_di = new \DateInterval('P' . abs($dayDelta) . 'D');
        $minutes_di = new \DateInterval('PT' . abs($minuteDelta) . 'M');

        //invert intervals if values are negative. Feature of DataTime class
        if ($dayDelta < 0) {
            $days_di->invert = 1;
        }

        if ($minuteDelta < 0) {
            $minutes_di->invert = 1;
        }

        $new_start_dt->add($days_di);
        $new_start_dt->add($minutes_di);

        $new_end_dt->add($days_di);
        $new_end_dt->add($minutes_di);

        if ($new_start_dt < $now_dt) {
            if (!$this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("Moving session to a past date not allowed", 0, \ACTIVITY_LOG_TYPE);
            }
        }

        if ($event->getStart() < $now_dt) {
            if (!$this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("Only future events are editable", 0, \SECURITY_LOG_TYPE);
                
            }
        }

        $event->setEnd($new_end_dt);
        $event->setStart($new_start_dt);
        
        $this->coreEventDAO->modifyEventTime($event);

        $log_text = "Source: " . __CLASS__ . "::" . __FUNCTION__ . " Event : $dec_record_id moved";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);
    }

    public function resizeEvent(CoreEventHTTPParams $params) {

        $dayDelta = $params->getDayDelta();
        $minuteDelta = $params->getMinuteDelta();
        
        $logged_in_user_id = $this->user->getUserID();

        $dec_record_id = $this->crypto->decrypt($params->getEncRecID());

        /* @var $event CoreEvent */
        $event = $this->coreEventDAO->getCoreEvent($dec_record_id, $params->getTimestamp());
        
        if(! $event instanceof CoreEvent )
        {
            $this->throwExceptionOnError ("Event not found or already modified", 0, \ERROR_LOG_TYPE);
        }

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $event->getServiceId(), $event->isOwner($logged_in_user_id));  
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $event->getServiceId());

        if (!$this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_EVENT)) {
            $this->throwExceptionOnError ("Insufficient user permissions", 0, \SECURITY_LOG_TYPE);
        }

        $now_dt = new \DateTime();
        
        $new_start_dt = $event->getStart();
        $new_end_dt = $event->getEnd();

        $days_di = new \DateInterval('P' . abs($dayDelta) . 'D');
        $minutes_di = new \DateInterval('PT' . abs($minuteDelta) . 'M');

        //invert intervals if values are negative. Feature of DataTime class
        if ($dayDelta < 0) {
            $days_di->invert = 1;
        }

        if ($minuteDelta < 0) {
            $minutes_di->invert = 1;
        }

        $new_end_dt->add($days_di);
        $new_end_dt->add($minutes_di);

        //Make sure that end time is after start time
        if ($new_end_dt <= $new_start_dt) {
            $this->throwExceptionOnError ("Start date and end date incorrect", 0, \SECURITY_LOG_TYPE);
        }
        
        if ($event->getStart() < $now_dt) {
            if (!$this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("Only future events are editable", 0, \SECURITY_LOG_TYPE);
            }
        }

        $event->setEnd($new_end_dt);
        
        $this->coreEventDAO->modifyEventTime($event);

        $log_text = "Source: " . __CLASS__ . "::" . __FUNCTION__ . " Event : " . $dec_record_id . " resized";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);
    }

    public function cancelEvent(\stdClass $eventoptions) {
        
        $logged_in_user_id = $this->user->getUserID();
        
        $dec_record_id = $this->crypto->decrypt($eventoptions->record_id);
        
        $timestamp_dt = new \DateTime($eventoptions->timestamp);

        $event = $this->coreEventDAO->getCoreEvent($dec_record_id,$timestamp_dt);
        
        if(! $event instanceof CoreEvent)
        {
            $this->throwExceptionOnError ("Event not found or already modified", 0, \ERROR_LOG_TYPE);
        }

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $event->getServiceId(), $event->isOwner($logged_in_user_id));  
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $event->getServiceId());


        //Check if user can delete an event
        if (!$this->permission_manager->hasPermission($permissions_a, \PERM_DELETE_EVENT)) {
            $this->throwExceptionOnError ("Permission denied", 0, \SECURITY_LOG_TYPE);
        }


        //Check if user can edit events in the past
        $now_dt = new \DateTime();
        if ($event->getStart() < $now_dt) {
            if (!$this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("Past session cannot be cancelled", 0, \SECURITY_LOG_TYPE);
            }
        }

        $event->setEventState(0);
        
        $this->coreEventDAO->saveCoreEvent($event);

        $log_text = "Source: " . __CLASS__ . "::" . __FUNCTION__ . "- SESSION ID: " . $dec_record_id . " CANCELED";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);

        return 1;
    }

    public function changeNote(\stdClass $eventoptions) {
        
        //Get current time
        $now_dt = new \DateTime();

        $timestamp_dt = new \DateTime($eventoptions->timestamp);
        
        $logged_in_user_id = $this->user->getUserID();
        
        $encrypted_record_id = $eventoptions->record_id;
        $record_id = $this->crypto->decrypt($encrypted_record_id);

        //Filter text
        $clean_text = filter_var($eventoptions->note, FILTER_SANITIZE_STRING);
        
        /* @var $event CoreEvent */
        $event = $this->coreEventDAO->getCoreEvent($record_id,$timestamp_dt);

        if(! $event instanceof CoreEvent)
        {
            $this->throwExceptionOnError ("Event not found or already modified", 0, \ERROR_LOG_TYPE);
        }
        
        $user_roles = UserRoleManager::getUserRolesForService($this->user, $event->getServiceId(), $event->isOwner($logged_in_user_id));  
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $event->getServiceId());

        //Check for DB_PERM_CHANGE_NOTE permission
        if (!$this->permission_manager->hasPermission($permissions_a, \PERM_CHANGE_NOTE)) {
            $this->throwExceptionOnError ("Missing permission: DB_PERM_CHANGE_NOTE ", 0, \SECURITY_LOG_TYPE);
        }

        $event->setNote($clean_text);
        
        if( ! $this->coreEventDAO->saveCoreEvent($event))
        {
            $this->throwExceptionOnError ("Data could not be saved.", 0, \ERROR_LOG_TYPE);
        }

        $log_text = __CLASS__ . ":" . __FUNCTION__ . " - Note for session: " . $record_id . " changed";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);

        return 1;
    }

    public function changeUser(\stdClass $params) {
        
        /*
         * TODO: Convert stdClass to a better defined $params class
         */
        if (!isset($params->id) ) {
            $this->throwExceptionOnError("Event id invalid", 0, \SECURITY_LOG_TYPE);
        }

        if (!isset($params->user_id)) {
             $this->throwExceptionOnError("User id invalid", 0, \SECURITY_LOG_TYPE);
        }
        
        if (!isset($params->timestamp)) {
             $this->throwExceptionOnError("Event timestamp invalid", 0, \SECURITY_LOG_TYPE);
        }

        //decrypt encrypted data coming back from the client
        $record_id = $this->crypto->decrypt($params->id);
        $new_user_id = $this->crypto->decrypt($params->user_id);
        $timestamp_dt = new \DateTime($params->timestamp);

        /* @var $event CoreEvent */
        $event = $this->coreEventDAO->getCoreEvent($record_id,$timestamp_dt);

        if(! $event instanceof CoreEvent)
        {
            $this->throwExceptionOnError ("Event not found or already modified", 0, \ERROR_LOG_TYPE);
        }
        
        //TODO: Remove later
        $service_id = $event->getServiceId();

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $event->getServiceId(), $event->isOwner($this->user->getUserID()));  
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $event->getServiceId());

        if (!$this->permission_manager->hasPermission($permissions_a, \PERM_CHANGE_OWNER)) {
            $this->throwExceptionOnError ( __FUNCTION__ . ": Insufficient user permissions", 0, \SECURITY_LOG_TYPE);
        }


        //Check if the new users has a role for a given service_id
        $check_user_q = "SELECT role FROM core_user_role WHERE user_id = ? and service_id = ?";

        if( ! $stmt = mysqli_prepare($this->connection, $check_user_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'ii', $new_user_id, $service_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_store_result($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $rows = mysqli_stmt_num_rows($stmt);

        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);

        if ($rows < 1) {
             $this->throwExceptionOnError (__CLASS__ . ":" . __FUNCTION__ . " - New user without roles.", 0, \SECURITY_LOG_TYPE);
        }

        $event->setUserId($new_user_id);

        $this->coreEventDAO->saveCoreEvent($event);

        $log_text = __CLASS__ . ":" . __FUNCTION__ . " - User for session: " . $record_id . " changed";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);

        return 1;
    }

    public function getAuthorizedUsers($encrypted_record_id) {

        $is_owner = false;


        if (is_null($encrypted_record_id)) {
            $this->throwExceptionOnError( __FUNCTION__ . " Invalid record ID ", 0, \SECURITY_LOG_TYPE);
        }
        
        $result_array = [];
        $service_id = null;

        $dec_record_id = $this->crypto->decrypt($encrypted_record_id);

        //---Get session details
        $query_id = "SELECT cta.service_id,cs.state FROM core_timed_activity cta,core_services cs WHERE cta.id = ? and cta.service_id = cs.id";

        if( ! $stmt = mysqli_prepare($this->connection, $query_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'i', $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();
        if( ! mysqli_stmt_bind_result($stmt, $temp->service_id, $temp->service_state)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (mysqli_stmt_fetch($stmt)) {
            $service_id = $temp->service_id;
            $service_state = $temp->service_state;
        }

        mysqli_stmt_close($stmt);

        //get the user's role for the selected service
        $user_roles = UserRoleManager::getUserRolesForService($this->user, $service_id, $is_owner);  
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $service_id);

        if (!$this->permission_manager->hasPermission($permissions_a, \PERM_CHANGE_OWNER)) {
            $this->throwExceptionOnError("Permission DB_PERM_CHANGE_OWNER missing", 0, \SECURITY_LOG_TYPE);
        }


        //Get users with a role for a given service
        $user_q = "SELECT cu.firstname,cu.lastname,cu.username,cu.id AS id FROM core_user_role cur,core_users cu WHERE cur.service_id = ? AND cur.user_id = cu.id ORDER BY cu.lastname";

        if( ! $stmt = mysqli_prepare($this->connection, $user_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'i', $service_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp_user = new \stdClass();

        if( ! mysqli_stmt_bind_result($stmt, $temp_user->firstname, $temp_user->lastname, $temp_user->username, $temp_user->user_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }


        while (mysqli_stmt_fetch($stmt)) {

            $user = new \stdClass();

            //Return user info in the format "John D. (johnd)"
            $user->text = $temp_user->firstname . " " . substr($temp_user->lastname, 0, 1) . ". (" . $temp_user->username . ")";
            $user->value = $this->crypto->encrypt($temp_user->user_id);

            $result_array[] = $user;
        }

        mysqli_stmt_close($stmt);

        return $result_array;
    }
    
    private function mergeCoreEvents(CoreEvent $new_event,CoreEvent $merge_target)
    {
        $now = new \DateTime();
     
        $user_roles = UserRoleManager::getUserRolesForService($this->user, $merge_target->getServiceId(),$merge_target->isOwner($this->user->getUserID()));
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $merge_target->getServiceId());
        
        if (!$this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_EVENT)) {
            $this->throwExceptionOnError("Extending event failed. Permission denied", 0, \ACTIVITY_LOG_TYPE);
        }

        if( $new_event->getStart() < $now)
        {
            if (!$this->permission_manager->hasPermission($permissions_a, \PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("Extending event into past now allowed", 0, \ACTIVITY_LOG_TYPE);
            }
        }
        
        if( $merge_target->getEnd() == $new_event->getStart())
        {
            $merge_target->setEnd($new_event->getEnd());
        }
        
        if( $merge_target->getStart() == $new_event->getEnd())
        {
            $merge_target->setStart($new_event->getStart());
        }
        
        $this->coreEventDAO->modifyEventTime($merge_target);
        
        $log_text = __CLASS__ . ":" . __FUNCTION__ . " Event " . $merge_target->getId() . " extended";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);
    }

}
