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

include_once __DIR__ . '/DbConnectInfo.php';

include_once __DIR__ . '/CoreComponent.php';
include_once __DIR__ . '/SystemConstants.php';
include_once __DIR__ . '/UserRoleManager.php';
include_once __DIR__ . '/../model/PermissionManager.php';
include_once __DIR__ . '/../model/CoreUser.php';
include_once __DIR__ . '/../model/EventDetails.php';

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\EventDetails as EventDetails;
use ccny\scidiv\cores\model\PermissionManager as PermissionManager;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\components\UserRoleManager as UserRoleManager;

/**
 * Description of EventDetailsHandler
 *
 * @author WORK 1328
 */
class EventDetailsHandler extends CoreComponent {

    //put your code here
    private $pm;
    
    private $key = "lENb2bPRk)c&k0ebY0nSxiq9iKgg8WYU";

    public function __construct(CoreUser $core_user) {

        parent::__construct();

        $this->user = $core_user;

        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        @$this->connection = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($this->connection->connect_errno) {
            $this->throwDBError($this->connection->connect_error, $this->connection->connect_errno);
        }

        $this->pm = new PermissionManager($this->connection);
    }

    /**
     * getEventDetails()
     * 
     * Returns array of values that can be then passed to the templating engine for rendering
     * 
     * @param type $encrypted_record_id
     * @return Array
     */
    public function getEventDetails($encrypted_record_id) {


        $is_owner = false;
        $logged_in_user_id = $this->user->getUserID();

        $now_dt = new \DateTime();

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $record_id = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($encrypted_record_id), MCRYPT_MODE_ECB, $iv));
        
        /* @var $raw_details EventDetails */
        $raw_details = $this->getData($record_id);
        
        $start_dt = new \DateTime($raw_details->start);
        $end_dt = new \DateTime($raw_details->end);
        
        
        if ($logged_in_user_id == $raw_details->user_id) {
            $is_owner = true;
        }

        $user_roles = UserRoleManager::getUserRolesForService($this->user, $raw_details->serv_id, $is_owner);
        $permissions_a = $this->pm->getPermissions($user_roles, $raw_details->serv_id);

        $ArrDetails = [];
        
        $ArrDetails['username'] = $raw_details->username;
        $ArrDetails['start'] = $start_dt->format("m/d/y g:i a");
        $ArrDetails['end'] = $end_dt->format("m/d/y g:i a");
        $ArrDetails['type'] = $raw_details->type;


        if ($this->pm->hasPermission($permissions_a, \DB_PERM_VIEW_DETAILS)) {

            $ArrDetails['record_id'] = $encrypted_record_id;

            /*
             * Show full name and username with DB_PERM_VIEW_DETAILS
             */
            $user_name_str = $raw_details->firstname . " " . substr($raw_details->lastname, 0, 1) . ". (" . $raw_details->username . ")";
            $ArrDetails['username'] = $user_name_str;

            $ArrDetails['email'] = $raw_details->email;
            $ArrDetails['pi'] = $raw_details->pi;
            $ArrDetails['note'] = $raw_details->note;
        }

        if ($this->pm->hasPermission($permissions_a, \DB_PERM_DELETE_EVENT)) {
            $ArrDetails['can_cancel'] = true;
        }

        if ($start_dt < $now_dt) {
            if (!$this->pm->hasPermission($permissions_a, \DB_PERM_EDIT_PAST_EVENT)) {
                unset($ArrDetails['can_cancel']);
            }
        }

        if ($this->pm->hasPermission($permissions_a, \DB_PERM_CHANGE_OWNER)) {
            $user_id_enc = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $raw_details->user_id, MCRYPT_MODE_ECB, $iv));
            $ArrDetails['user_id'] = $user_id_enc;
            $ArrDetails['can_edit_user'] = true;
        }


        //If user can change note
        if ($this->pm->hasPermission($permissions_a, \DB_PERM_CHANGE_NOTE)) {
            if ($start_dt > $now_dt) {
                $ArrDetails['can_edit_note'] = true;
            } else {
                if ($this->pm->hasPermission($permissions_a, \DB_PERM_EDIT_PAST_EVENT)) {
                    $ArrDetails['can_edit_note'] = true;
                }
            }
        }

        return $ArrDetails;
    }
    
    /**
     * getData()
     *
     * Returns EventDetails.
     * 
     * @param int $record_id Integer Unique session id in the DB
     * 
     *   
     */
    private function getData($record_id)
    {
        
        $details = new EventDetails();
        
        $query = "SELECT cs.state,cs.short_name,cu.id,cu.firstname,cu.lastname,cu.username,cu.email,concat(p.first_name,' ',p.last_name) as piname,cta.service_id,cta.start,cta.end,cta.note FROM core_timed_activity cta, core_users cu,core_services cs, people p WHERE cta.id = ? AND cu.id = cta.user AND cs.id = cta.service_id AND p.individual_id = cu.pi";

        if (!$stmt = mysqli_prepare($this->connection, $query)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_bind_param($stmt, 'i', $record_id)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_execute($stmt)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_bind_result($stmt, $details->service_state, $details->type, $details->user_id, $details->firstname, $details->lastname, $details->username, $details->email, $details->pi, $details->serv_id, $details->start, $details->end, $details->note)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (! mysqli_stmt_fetch($stmt)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        
        mysqli_stmt_close($stmt);
        
        return $details;
    }

}
