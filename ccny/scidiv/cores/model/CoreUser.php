<?php

namespace ccny\scidiv\cores\model;

class CoreUser {

    private $user_id;
    public $user_name;
    public $user_type;
    public $last_active;
    public $pi_name;

    /* @var $authenticated Boolen. Is the user authenticated */
    private $authenticated;

    /* @var $roles. array(service_id => role_id) */
    private $roles;

    public function __construct($user_name) {
        /*
         * Initialize user
         */
        $this->last_active = null;
        $this->pi_name = null;
        $this->user_id = null;
        $this->user_name = $user_name;
        $this->user_type = null;
        $this->authenticated = false;

        $this->roles = [];
    }

    public function updateUserInfo($user_id, $user_type, $last_active, $pi_name) {
        if ($this->authenticated) {
            $this->user_id = $user_id;
            $this->user_type = $user_type;
            $this->last_active = $last_active;
            $this->pi_name = $pi_name;
        }
    }

    public function setRoles($roles) {
        if (is_array($roles)) {
            $this->roles = $roles;
        }
    }

    public function getRoles() {
        return $this->roles;
    }

    public function setAuth($auth) {
        $this->authenticated = $auth;
    }
    
    public function getUserName() {
        return $this->user_name;
    }
    
    public function isAuth()
    {
        return $this->authenticated;
    }
    
    public function getUserID()
    {
        return $this->user_id;
    }

}
