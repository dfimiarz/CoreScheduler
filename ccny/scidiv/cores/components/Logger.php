<?php

/*
  Class used to log error messages to a file spcified in the const ERROR_FILE
 */
namespace ccny\scidiv\cores\components;

include_once './../autoloader.php';
include_once 'SystemConstants.php';

class Logger {
    /*
      Rember to place files in a directory with correct write permissions.
      On unix this could  be /tmp
     */

    /*
     * UNIX log directory
     */
    //const LOG_DIRECTORY = '/var/log/corelabs/';
    
    /*
     * WINDOWS log directory
     */
    const LOG_DIRECTORY = 'C:/weblogs/';
    
    
    const ERROR_FILE = 'CORELABS_ERROR.log';
    const WARNING_FILE = 'CORELABS_WARNING.log';
    const SECURITY_FILE = 'CORELABS_SECURITY.log';
    const DATABASE_FILE = 'CORELABS_DATABASE.log';
    const ACTIVITY_FILE = 'CORELABS_ACTIVITY.log';

    private function __construct() {
        
        
    }

    public static function log($msg, $error_type) {
        
       
        $date = date('d.m.Y h:i:s');

        $log = "DATE:  " . $date . "| " . $msg;
        
        $log_dest = self::ERROR_FILE;

        /*
          Choose different log file and modify log string
          depending on the type of logging requested
         */

        if ($error_type == DATABASE_LOG_TYPE) {
            $log_dest = self::DATABASE_FILE;
        }

        if ($error_type == WARNING_LOG_TYPE) {
            $log_dest = self::WARNING_FILE;
        }

        if ($error_type == ACTIVITY_LOG_TYPE) {
            $log_dest = self::ACTIVITY_FILE;
        }

        if ($error_type == SECURITY_LOG_TYPE) {
            $log_dest = self::SECURITY_FILE;
            $log = $log . " | REMOTE IP: " . $_SERVER['REMOTE_ADDR'];
        }

        $log = $log . "\n";

        $log_dest = self::LOG_DIRECTORY . $log_dest;

        error_log($log, 3, $log_dest);
    }

}
