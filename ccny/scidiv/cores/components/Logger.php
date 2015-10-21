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

use ccny\scidiv\cores\config\Config as Config;
/*
  Class used to log error messages to a file spcified in the const ERROR_FILE
 */

class Logger {
    
    private $log_dir;
    
    private $error_file_name;
    private $warning_file_name;
    private $security_file_name;
    private $database_file_name;
    private $activity_file_name;

    public function __construct() {
        
        $this->log_dir = Config::LOG_DIR;
        
        $this->activity_file_name = Config::APP_ID . '_ACTIVITY.log';
        $this->database_file_name = Config::APP_ID . '_DATABASE.log';
        $this->security_file_name = Config::APP_ID . '_SECURITY.log';
        $this->warning_file_name = Config::APP_ID . '_WARNING.log';
        $this->error_file_name = Config::APP_ID . '_ERROR.log';
    }

    public function log($msg, $error_type) {
        
       
        $date = date('d.m.Y h:i:s');

        $log = "DATE:  " . $date . "| " . $msg;
        
        $log_dest = $this->error_file_name;

        /*
          Choose different log file and modify log string
          depending on the type of logging requested
         */

        if ($error_type == DATABASE_LOG_TYPE) {
            $log_dest = $this->database_file_name;
        }

        if ($error_type == WARNING_LOG_TYPE) {
            $log_dest = $this->warning_file_name;
        }

        if ($error_type == ACTIVITY_LOG_TYPE) {
            $log_dest = $this->activity_file_name;
        }

        if ($error_type == SECURITY_LOG_TYPE) {
            $log_dest = $this->security_file_name;
            $log = $log . " | REMOTE IP: " . $_SERVER['REMOTE_ADDR'];
        }

        $log = $log . "\n";

        $log_dest = $this->log_dir . $log_dest;

        error_log($log, 3, $log_dest);
    }

}
