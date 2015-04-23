<?php
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
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\PermissionManager as PermissionManager;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\components\UserRoleManager as UserRoleManager;
use ccny\scidiv\cores\model\CoreEvent as CoreEvent;

class ScheduleDataHandler extends CoreComponent {

    /* @var $user CoreUser */
    private $user;
    
    private $connection;
    private $color_selector;
   
    private $permission_manager;
    private $key = "lENb2bPRk)c&k0ebY0nSxiq9iKgg8WYU";

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
    }

    function __destruct() {

        mysqli_close($this->connection);
    }

    function createEvent($event_options) {

        $is_owner = false;

        //The function assumes that the data is validated

        $start_dt = new \DateTime();
        $start_dt->setTimestamp($event_options->start);

        $end_dt = new \DateTime();
        $end_dt->setTimestamp($event_options->end);

        $service_id = $event_options->service_id;

        $all_day = $event_options->all_day;

        $state = 1;
        $user_id = $this->user->getUserID();


        //Creating a session in the past is restricted to DB_ADMIN only
        $now = new \DateTime();

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $service_id, $is_owner);
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $service_id);

        if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_CREATE_EVENT)) {
            $this->throwExceptionOnError ("Insufficient user permissions", 0, \SECURITY_LOG_TYPE);
        }

        if ($start_dt < $now) {
            if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("Adding a session in the past not allowed", 0, \SECURITY_LOG_TYPE);
            }
        }

        //---End: Check service state
        //---START: ALL DAY CHECK
        //Check if an all_day event is requested
        //if( $all_day && $user_role != DB_ADMIN )
        //	$this->throwCustomExceptionOnError("Only administrator can create all day events");
        //---END ALL DAY CHECK
        //Lock tables
        $lock_q = "LOCK TABLES core_timed_activity WRITE, core_services AS cs1 WRITE, core_services AS cs2 WRITE";
       
        if (!$this->connection->query($lock_q)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        

        //check if the selected timeframe is already taken
        $check_q = "SELECT IF( COUNT(1),0,1 ) AS Available FROM core_timed_activity WHERE service_id in (SELECT id FROM core_services AS cs1 WHERE resource_id = (SELECT resource_id FROM core_services AS cs2 WHERE id = ?)) AND state = 1 AND start < ? AND end > ?";
        $start_time_str = $start_dt->format('Y-m-d H:i:s');
        $end_time_str = $end_dt->format('Y-m-d H:i:s');

        if (!$stmt = $this->connection->prepare($check_q)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!$stmt->bind_param('iss', $service_id, $end_time_str, $start_time_str)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $available = 0;

        $stmt->bind_result($available);

        $stmt->fetch();

        $stmt->free_result();
        $stmt->close();

        if (!$available) {
            $this->throwExceptionOnError ("Timeslot already taken", 0, \ACTIVITY_LOG_TYPE);
        }

        $insert_q = "INSERT INTO `core_timed_activity`
							(`id`,
						`service_id`,
						`time_recorded`,
						`time_modified`,
						`state`,
						`start`,
						`end`,
						`user`,
						`note`)
						VALUES
						(
						null,
						?,
						NOW(),
						NOW(),
						?,
						?,
						?,
						?,
						''
						)";


        if (!$stmt = $this->connection->prepare($insert_q)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $start_time_str = $start_dt->format('Y-m-d H:i:s');
        $end_time_str = $end_dt->format('Y-m-d H:i:s');


        if (!$stmt->bind_param('iissi', $service_id, $state, $start_time_str, $end_time_str, $user_id)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

       if (!$stmt->execute()) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
       }

        $last_id = $stmt->insert_id;

        //Unlock tables
        $unlock_q = "UNLOCK TABLES";
       
        if (!$this->connection->query($unlock_q)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $log_text = "Source: " . __CLASS__ . "::" . __FUNCTION__ . " - SESSION " . $last_id . " CREATED";
        
        $this->log($log_text, \ACTIVITY_LOG_TYPE);

        return 1;
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
        $query = "SELECT cta.id,cta.service_id,cs.short_name,cu.id,cu.username,cta.start,cta.end,cta.note,cs.state FROM core_timed_activity cta, core_users cu,core_services cs WHERE cta.start <= ? AND cta.end >= ? AND cta.service_id IN (SELECT id from core_services WHERE resource_id = ? ) AND cta.state = 1 AND cu.id = cta.user AND cs.id = cta.service_id";

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

        $stmt->bind_result($temp->id, $temp->service_id, $temp->short_name, $temp->user_id, $temp->username, $temp->start, $temp->end, $temp->note, $temp->service_state);
        
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
            $event->id = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $temp_event->id, MCRYPT_MODE_ECB, $iv));

            $event->title = $temp_event->username;

            $event->description = $temp_event->short_name;
            $event->start = $t_start->format("Y-m-d\TH:i:s\Z");
            $event->end = $t_end->format("Y-m-d\TH:i:s\Z");

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
            if ($this->permission_manager->hasPermission($permissions_a, \DB_PERM_EDIT_EVENT)) {
                $event->editable = true;
            }

            if ($t_start < $now_dt) {
                if ($this->permission_manager->hasPermission($permissions_a, \DB_PERM_EDIT_PAST_EVENT)) {
                    $event->editable = true;
                } else {
                    $event->editable = false;
                }
            }

            //Determine visibility of an event
            if ($this->permission_manager->hasPermission($permissions_a, \DB_PERM_VIEW_EVENT)) {
                $result_array[] = $event;
            }
        }

        return $result_array;
    }

    public function moveEvent($record_id, $dayDelta, $minuteDelta, $allDay) {
        
        $is_owner = false;
        $logged_in_user_id = $this->user->getUserID();
        $service_id = null;

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $dec_record_id = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($record_id), MCRYPT_MODE_ECB, $iv));

        //---Get session details
        $session_query = "SELECT cta.start,cta.end,cta.user,cta.service_id,cs.state FROM core_timed_activity cta,core_services cs WHERE cta.id = ? and cta.service_id = cs.id";

        if( ! $stmt = mysqli_prepare($this->connection, $session_query)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'i', $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();
        if( ! mysqli_stmt_bind_result($stmt, $temp->start, $temp->end, $temp->user_id, $temp->service_id, $temp->state)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (mysqli_stmt_fetch($stmt)) {
            $start_dt = new \DateTime($temp->start);
            $end_dt = new \DateTime($temp->end);
            $service_id = $temp->service_id;
            $user_id = $temp->user_id;
            $service_state = $temp->state;
        }

        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
        //---

        if ($logged_in_user_id == $user_id) {
            $is_owner = true;
        }

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $service_id, $is_owner);
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $service_id);

        if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_EDIT_EVENT)) {
            $this->throwExceptionOnError ("Insufficient user permissions", 0, \SECURITY_LOG_TYPE);
        }

        //Only DB_ADMIN can modify past session
        $now_dt = new \DateTime();

        $new_start_dt = $start_dt;
        $new_end_dt = $end_dt;

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
            if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("Moving session to a past date not allowed", 0, \ACTIVITY_LOG_TYPE);
            }
        }

        if ($start_dt < $now_dt) {
            if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("You cannot move past session", 0, \SECURITY_LOG_TYPE);
                
            }
        }


        //Lock tables before edit
        $lock_q = "LOCK TABLES core_timed_activity WRITE, core_services AS cs1 WRITE, core_services AS cs2 WRITE";
        if( ! mysqli_query($this->connection, $lock_q) ){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        //check if the selected timeframe is already taken
        $check_q = "SELECT IF( COUNT(1),0,1 ) AS Available FROM core_timed_activity WHERE service_id in (SELECT id FROM core_services AS cs1 WHERE resource_id = (SELECT resource_id FROM core_services AS cs2 WHERE id = ?)) AND state = 1 AND start < ? AND end > ? AND id <> ?";
        $new_start_time_str = $new_start_dt->format('Y-m-d H:i:s');
        $new_end_time_str = $new_end_dt->format('Y-m-d H:i:s');

        if( ! $stmt = mysqli_prepare($this->connection, $check_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'issi', $service_id, $new_end_time_str, $new_start_time_str, $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $available = 0;

        if( ! mysqli_stmt_bind_result($stmt, $available)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        mysqli_stmt_fetch($stmt);

        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);

        if (!$available) {
            $this->throwExceptionOnError ("Selected time slot already taken.", 0, \ACTIVITY_LOG_TYPE);
        }

        //update the record
        $interval_minutes = $dayDelta * 1440 + $minuteDelta;

        $query = "UPDATE core_timed_activity SET start = DATE_ADD( start, INTERVAL ? MINUTE ),end = DATE_ADD( end, INTERVAL ? MINUTE ) WHERE id = ?";

        if( ! $stmt = mysqli_prepare($this->connection, $query)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'iii', $interval_minutes, $interval_minutes, $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
                
        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        mysqli_stmt_close($stmt);

        $log_text = "Source: " . __CLASS__ . "::" . __FUNCTION__ . " SESSION ID: $dec_record_id MOVED";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);
    }

    public function resizeEvent($record_id, $dayDelta, $minuteDelta) {

        $is_owner = false;
        $logged_in_user_id = $this->user->getUserID();
        $service_id = null;

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $dec_record_id = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($record_id), MCRYPT_MODE_ECB, $iv));


        //---Get session details
        $query = "SELECT cta.start,cta.end,cta.user,cta.service_id,cs.state FROM core_timed_activity cta, core_services cs WHERE cta.id = ? and cta.service_id = cs.id";

        if( ! $stmt = mysqli_prepare($this->connection, $query)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'i', $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();
        if( ! mysqli_stmt_bind_result($stmt, $temp->start, $temp->end, $temp->user_id, $temp->service_id, $temp->state)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (mysqli_stmt_fetch($stmt)) {
            $start_dt = new \DateTime($temp->start);
            $end_dt = new \DateTime($temp->end);
            $service_id = $temp->service_id;
            $user_id = $temp->user_id;
            $service_state = $temp->state;
        }

        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
        //---

        if ($logged_in_user_id == $user_id) {
            $is_owner = true;
        }

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $service_id, $is_owner);  
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $service_id);

        if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_EDIT_EVENT)) {
            $this->throwExceptionOnError ("Insufficient user permissions", 0, \SECURITY_LOG_TYPE);
        }

        //Only DB_ADMIN can modify past session
        $now_dt = new \DateTime();

        $new_start_dt = $start_dt;
        $new_end_dt = $end_dt;

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

        //Lock tables before edit
        $lock_q = "LOCK TABLES core_timed_activity WRITE, core_services AS cs1 WRITE, core_services AS cs2 WRITE";
        if( ! mysqli_query($this->connection, $lock_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        //check if the selected timeframe is already taken
        $check_q = "SELECT IF( COUNT(1),0,1 ) AS Available FROM core_timed_activity WHERE service_id in (SELECT id FROM core_services AS cs1 WHERE resource_id = (SELECT resource_id FROM core_services AS cs2 WHERE id = ?)) AND state = 1 AND start < ? AND end > ? AND id <> ?";
        $new_start_time_str = $start_dt->format('Y-m-d H:i:s');
        $new_end_time_str = $new_end_dt->format('Y-m-d H:i:s');

        if( ! $stmt = mysqli_prepare($this->connection, $check_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'issi', $service_id, $new_end_time_str, $new_start_time_str, $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $available = 0;

        if( ! mysqli_stmt_bind_result($stmt, $available)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        mysqli_stmt_fetch($stmt);

        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);

        if (!$available) {
             $this->throwExceptionOnError ("Timeslot already reserved", 0, \ACTIVITY_LOG_TYPE);
        }

        $interval_minutes = $dayDelta * 1440 + $minuteDelta;

        $update_query = "UPDATE core_timed_activity SET end = DATE_ADD( end, INTERVAL ? MINUTE ) WHERE id = ?";

        if( ! $stmt = mysqli_prepare($this->connection, $update_query)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'ii', $interval_minutes, $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        mysqli_stmt_close($stmt);

        $log_text = "Source: " . __CLASS__ . "::" . __FUNCTION__ . " SESSION ID: " . $dec_record_id . " RESIZED";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);
    }

    public function cancelEvent($encrypted_record_id) {
        
        $is_owner = false;
        $service_id = null;

        
        $logged_in_user_id = $this->user->getUserID();
        
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $dec_record_id = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($encrypted_record_id), MCRYPT_MODE_ECB, $iv));

        //---Get session details
        $query = "SELECT cta.start,cta.end,cta.user,cta.service_id,cs.state FROM core_timed_activity cta,core_services cs WHERE cta.id = ? and cta.service_id = cs.id";

        if( ! $stmt = mysqli_prepare($this->connection, $query)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'i', $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();
        
        if( ! mysqli_stmt_bind_result($stmt, $temp->start, $temp->end, $temp->user_id, $temp->service_id, $temp->service_state)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        
        if (mysqli_stmt_fetch($stmt)) {
            $start_dt = new \DateTime($temp->start);
            $end_dt = new \DateTime($temp->end);
            $service_id = $temp->service_id;
            $user_id = $temp->user_id;
            $service_state = $temp->service_state;
        }

        mysqli_stmt_close($stmt);

        //---

        if ($logged_in_user_id == $user_id) {
            $is_owner = true;
        }

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $service_id, $is_owner);  
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $service_id);


        //Check if user can delete an event
        if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_DELETE_EVENT)) {
            $this->throwExceptionOnError ("Permission denied", 0, \SECURITY_LOG_TYPE);
        }


        //Check if user can edit events in the past
        $now_dt = new \DateTime();
        if ($start_dt < $now_dt) {
            if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_EDIT_PAST_EVENT)) {
                $this->throwExceptionOnError ("Past session cannot be cancelled", 0, \SECURITY_LOG_TYPE);
            }
        }

        $cancel_q = "UPDATE `core_timed_activity` SET state = 0 WHERE id = ?";

        if( ! $stmt = mysqli_prepare($this->connection, $cancel_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'i', $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        mysqli_stmt_close($stmt);

        $log_text = "Source: " . __CLASS__ . "::" . __FUNCTION__ . "- SESSION ID: " . $dec_record_id . " CANCELED";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);

        return 1;
    }

    public function changeNote($encrypted_record_id, $text) {
        
        //Get current time
        $now_dt = new \DateTime();

        $logged_in_user_id = $this->user->getUserID();
        
        $record_id = $this->decryptRecordId($encrypted_record_id);

        //Filter text
        $clean_text = filter_var($text, FILTER_SANITIZE_STRING);

       /* @var $event CoreEvent */
        $event = $this->getCoreEvent($record_id);

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $event->getServiceId(), $event->isOwner($logged_in_user_id));  
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $event->getServiceId());

        //Check for DB_PERM_CHANGE_NOTE permission
        if ($this->permission_manager->hasPermission($permissions_a, \DB_PERM_CHANGE_NOTE)) {
            //Check if user can edit past event: DB_PERM_EDIT_PAST_EVENT
            if ($event->getStart() <= $now_dt) {
                if (!$this->permission_manager->hasPermission($permissions_a, DB_PERM_EDIT_PAST_EVENT)) {
                    $this->throwExceptionOnError ("Past sessions cannot be modified", 0, \SECURITY_LOG_TYPE);
                }
            }
        } else {
             $this->throwExceptionOnError ("Missing permission: DB_PERM_CHANGE_NOTE ", 0, \SECURITY_LOG_TYPE);
        }

        $event->setNote($clean_text);
        
        $this->saveCoreEvent($event);

        $log_text = __CLASS__ . ":" . __FUNCTION__ . " - Note for session: " . $record_id . " changed";
        $this->log($log_text, \ACTIVITY_LOG_TYPE);

        return 1;
    }

    public function changeUser($encrypted_record_id, $encrypted_user_id) {
        $is_owner = false;
        $logged_in_user_id = $this->user->getUserID();
        $service_id = null;

        if (is_null($encrypted_record_id) ) {
            $this->throwExceptionOnError("Record ID is invalid", 0, \SECURITY_LOG_TYPE);
        }

        if (is_null($encrypted_user_id)) {
             $this->throwExceptionOnError("New user ID is invalid", 0, \SECURITY_LOG_TYPE);
        }

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        //decrypt encrypted data coming back from the client
        $record_id = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($encrypted_record_id), MCRYPT_MODE_ECB, $iv));

        $new_user_id = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($encrypted_user_id), MCRYPT_MODE_ECB, $iv));

        //---Get session service_id
        $query_id = "SELECT cta.user,cta.service_id,cs.state FROM core_timed_activity cta,core_services cs WHERE cta.id = ? and cta.service_id = cs.id";

        if( ! $stmt = mysqli_prepare($this->connection, $query_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'i', $record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();
        if( ! mysqli_stmt_bind_result($stmt, $temp->user_id, $temp->service_id, $temp->service_state)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (mysqli_stmt_fetch($stmt)) {
            $service_id = $temp->service_id;
            $service_state = $temp->service_state;
            $user_id = $temp->user_id;
        }
        
        mysqli_stmt_close($stmt);

        if ($logged_in_user_id == $user_id) {
            $is_owner = true;
        }

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $service_id, $is_owner);  
        $permissions_a = $this->permission_manager->getPermissions($user_roles, $service_id);


        if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_CHANGE_OWNER)) {
            $this->throwExceptionOnError (__CLASS__ . ":" . __FUNCTION__ . " - Insufficient user permissions", 0, \SECURITY_LOG_TYPE);
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


        $change_user_q = "UPDATE core_timed_activity SET user = ? WHERE id = ?";

        if( ! $stmt = mysqli_prepare($this->connection, $change_user_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'ii', $new_user_id, $record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        mysqli_stmt_close($stmt);

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

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $dec_record_id = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($encrypted_record_id), MCRYPT_MODE_ECB, $iv));

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

        if (!$this->permission_manager->hasPermission($permissions_a, \DB_PERM_CHANGE_OWNER)) {
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
            $user->value = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $temp_user->user_id, MCRYPT_MODE_ECB, $iv));

            $result_array[] = $user;
        }

        mysqli_stmt_close($stmt);

        return $result_array;
    }
    
    /** Retrieves information about the event from the database
     * 
     * @param type $dec_record_id
     * @return CoreEvent
     */
    private function getCoreEvent($dec_record_id)
    {
        /* @var $event CoreEvent */
        $event = new CoreEvent($dec_record_id);
        
         //---Get session details
        $query = "SELECT cta.start,cta.end,cta.user,cta.service_id,cta.state as eventstate,cs.state servicestate FROM core_timed_activity cta,core_services cs WHERE cta.id = ? and cta.service_id = cs.id";

        if( ! $stmt = mysqli_prepare($this->connection, $query)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'i', $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();
        
        if( ! mysqli_stmt_bind_result($stmt, $temp->start, $temp->end, $temp->user_id, $temp->service_id,$temp->event_state, $temp->service_state)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        
        if (mysqli_stmt_fetch($stmt)) {
            $event->setStart(new \DateTime($temp->start));
            $event->setEnd(new \DateTime($temp->end));
            $event->setServiceId($temp->service_id);
            $event->setUserId($temp->user_id);
            $event->setServiceState($temp->service_state);
            $event->setEventState($temp->event_state);
        }

        mysqli_stmt_close($stmt);
        
        return $event;
    }
    
    /** Saves the event in the database
     * 
     * @param CoreEvent $event
     */
    private function saveCoreEvent(CoreEvent $event)
    {
        $user_id = $event->getUserId();
        $note = $event->getNote();
        $start = $event->getStart();
        $end = $event->getEnd();
        $state = $event->getEventState();
        $record_id = $event->getId();
        
        $start_str = $start->format('Y-m-d H:i:s');
        $end_str = $end->format('Y-m-d H:i:s');
        
        $change_user_q = "UPDATE core_timed_activity SET user = ?,note = ?,start = ?,end = ?,state = ? WHERE id = ?";

        if( ! $stmt = mysqli_prepare($this->connection, $change_user_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'isssii', $user_id, $note, $start_str, $end_str ,$state ,$record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        mysqli_stmt_close($stmt);
    }
    
    /** Decrypts record id sent by the client
     * 
     * @param type $encrypted_record_id
     * @return type int
     */
    private function decryptRecordId($encrypted_record_id)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $record_id = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($encrypted_record_id), MCRYPT_MODE_ECB, $iv));
        
        return $record_id;
        
    }

}
