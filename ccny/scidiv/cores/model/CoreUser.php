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

        $this->roles = array();
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
