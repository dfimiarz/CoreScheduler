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

namespace ccny\scidiv\cores\components\auth;

use ccny\scidiv\cores\components\auth\UserAuth as UserAuth;

class LDAPAuth extends UserAuth
{

    private $ldaphost = '134.74.48.10';
    private $ldapport = 389;
    private $ldaprdl = '@itcs.ccny.lan';
    private $ldap_connection;

    function __construct() {

        parent::__construct();
    }

    function authenticate($username, $password) {
        $authenticated = false;

        $this->ldap_connection = ldap_connect($this->ldaphost, $this->ldapport);

        if (!$this->ldap_connection) {
            $this->throwDBError(__CLASS__ . ": FAILED TO CONNECT TO AUTH SERVER", 0 );
        }

        ldap_set_option($this->ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);

        // binding to ldap server

        $username_ldap = $username . $this->ldaprdl;

        $ldapbind = @ldap_bind($this->ldap_connection, $username_ldap, $password);

        if ($ldapbind) {
            $authenticated = true;
            
        } else {

            $err = ldap_errno($this->ldap_connection);

            switch ($err) {
                case 49:
                    break;
                default:
                   $this->throwDBError(__CLASS__ . ": LDAP BIND FAILED", $err );
            }
        }

        return $authenticated;
    }

}