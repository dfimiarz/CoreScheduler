<?php
/*
 Change log
 04/17/2014 - Changed most sql calls to OOP style


*/
namespace ccny\scidiv\cores\model;

include_once __DIR__ . '/../components/DbConnectInfo.php';
include_once __DIR__ . '/../components/SystemConstants.php';
include_once __DIR__ . '/../components/CoreComponent.php';
include_once __DIR__ . '/CoreUser.php';
include_once __DIR__ . '/CoreRole.php';

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\components\auth\UserAuth as UserAuth;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\model\CoreRole as CoreRole;

class LoginManager extends CoreComponent
{

    /* @var $user CoreUser */
    private $user;

    // A private constructor; prevents direct creation of object
    public function __construct(CoreUser $user)
    {
        parent::__construct(); 
        
        $this->user = $user;
    }
    
    public function authenticateUser($password)
    {
        $is_authenicated = false;
        
        $user_name = $this->user->getUserName();
        
        /* @var $authenticators. An array of supported authenticators */
        $authenticators = array("ccny\scidiv\cores\components\auth\LDAPAuth","ccny\scidiv\cores\components\auth\MySQLAuth");

        foreach ($authenticators as $auth_name) {
            
            try {
                /* @var $auth UserAuth */
                $auth = new $auth_name();
                $is_authenicated = $auth->authenticate( $user_name, $password);
                
            } catch ( \Exception $e ) {
                
                $this->log("AUTH ERROR: " . $auth_name . ". MSG: " . $e->getMessage(), SECURITY_LOG_TYPE);
            
            }

            if ($is_authenicated) {
                $this->log($user_name . " AUTH WITH: " . $auth_name, SECURITY_LOG_TYPE);
                break;
            } else {
                $this->log($user_name . " AUTH FAILED WITH: " . $auth_name, SECURITY_LOG_TYPE);
            }
        }

        $this->user->setAuth($is_authenicated);
        
    }

    public function getAccountInfo()
    {
        $username = $this->user->getUserName();
        
        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        @$mysqli = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($mysqli->connect_errno) {
            $this->throwDBError($mysqli->connect_error, $mysqli->connect_errno);
        }

        /*
         * Get details for a user with a given $username
         */
        $user_info_q = "SELECT u.id,u.username,u.last_active,concat(p.first_name,' ',p.last_name) as pi,ut.short_name as user_type FROM core_users u,people p,core_user_types ut WHERE u.username = ? AND u.active_flag = 1 AND u.pi = p.individual_id AND ut.id = u.user_type limit 1";
        if (!$stmt = $mysqli->prepare($user_info_q)) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        if (!$stmt->bind_param('s', $username)) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        //store the result in memory
        if (!$stmt->store_result()) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        //get number of rows returned
        $rows = $stmt->num_rows;
       
        $temp = new \stdClass();
        $stmt->bind_result($temp->id, $temp->name, $temp->last_active, $temp->pi, $temp->type);

        if ($stmt->fetch()) {
            $this->user->updateUserInfo($temp->id, $temp->type, $temp->last_active, $temp->pi);
        }
        
        $stmt->free_result();
        $stmt->close();
        
        /*
         * If number of rows is not 1, singal that user info cannot not be loaded
         */
        if( $rows != 1 )
        {
            return false;
        }
        
        //Get user roles
        $user_id = $this->user->getUserID();
        $roles = [];
        $role_q = "SELECT cur.service_id,cur.role,cr.name FROM core_user_role cur,core_role cr WHERE cur.user_id = ? and cur.role = cr.id";

        if (!$stmt = $mysqli->prepare($role_q)) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        if (!$stmt->bind_param('i', $user_id)) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($mysqli->error, $mysqli->errno);
        }

        $temp = new \stdClass();

        $stmt->bind_result($temp->service, $temp->role_id,$temp->role_name);

        while ($stmt->fetch()) {
            //store roles in an array.
            $roles[$temp->service] = new CoreRole($temp->role_id,$temp->role_name);
        }

        $stmt->free_result();
        $stmt->close();
        
        $mysqli->close();
        
        $this->user->setRoles($roles);
        
        return true;
        
    } 

}
