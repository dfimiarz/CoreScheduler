<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseComponent
 *
 * @author WORK 1328
 */
namespace ccny\scidiv\cores\components;

include_once 'SystemConstants.php';
include_once 'Logger.php';

use ccny\scidiv\cores\components\Logger as Logger;

class CoreComponent {

    protected function __construct() {
        
        
    }
    
    protected function log($msg, $log_type)
    {
        Logger::log($msg, $log_type);
    }
    
    protected function throwExceptionOnError($errmsg, $errno, $log_type) {
        Logger::log($errmsg, $log_type);
        throw new \Exception($errmsg, $errno);
    }
    
    protected function throwDBError($errmsg, $errno )
    {
        $this->throwExceptionOnError($errmsg, $errno, \DATABASE_LOG_TYPE);
    }

}
