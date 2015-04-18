<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ccny\scidiv\cores\components\auth;

include_once __DIR__ . '\..\SystemConstants.php';

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