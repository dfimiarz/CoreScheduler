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

abstract class Logger {
    
    static public function log($msg, $error_type) {
        
       
        $date = date('d.m.Y h:i:s');

        if ($error_type == DATABASE_LOG_TYPE) {
            $log = "-- DATBASE --";
        }

        if ($error_type == WARNING_LOG_TYPE) {
            $log = "-- WARNING --";
        }

        if ($error_type == ACTIVITY_LOG_TYPE) {
            $log = "-- ACTIVITY --";
        }

        if ($error_type == SECURITY_LOG_TYPE) {
            $log = "-- SECURITY --";
        }
        
        $log .= " | " . $_SERVER['REMOTE_ADDR'] . " | ";
        
        $log .= " | " . $date . " | " . $msg;

        $log .= "\n";

        $log_dest = Config::LOG_DIR . Config::LOG_FILE;

        error_log($log, 3, $log_dest);
    }

}
