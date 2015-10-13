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

include_once __DIR__ . '/SystemConstants.php';

/*
  Class used to log error messages to a file spcified in the const ERROR_FILE
 */

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
