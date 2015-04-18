<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ccny\scidiv\cores\components\auth;

include_once(__DIR__ . '\..\CoreComponent.php');
include_once(__DIR__ . '\..\SystemConstants.php');

abstract class UserAuth extends \ccny\scidiv\cores\components\CoreComponent {
    
    public function __construct() {
        parent::__construct();
    }
    
    abstract function authenticate($username,$password);
}
