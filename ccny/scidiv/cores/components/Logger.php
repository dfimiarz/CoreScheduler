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

use ccny\scidiv\cores\config\Config as Config;

/*
  Class used to log error messages to a file spcified in the const ERROR_FILE
 */

abstract class Logger {

    static public function log($msg, $loglvl) {


        $date = date('d.m.Y h:i:s');
        
        $logline = "";

        switch ($loglvl) {
            case DATABASE_LOG_TYPE:
                $logline = "-- DATBASE --";
                break;

            case WARNING_LOG_TYPE:
                $logline = "-- WARNING --";
                break;

            case ACTIVITY_LOG_TYPE:
                $logline = "-- ACTIVITY --";
                break;

            case SECURITY_LOG_TYPE:
                $logline = "-- SECURITY --";
                break;

            case ERROR_LOG_TYPE:
                $logline = "-- ERROR --";
                break;

            default:
                $logline = "-- UNKNOWN --";
                break;
        }

        $logline .= " | " . $_SERVER['REMOTE_ADDR'] . " | ";

        $logline .= " | " . $date . " | " . $msg;

        $logline .= PHP_EOL;

        $log_dest = Config::LOG_DIR . Config::LOG_FILE;

        error_log($logline, 3, $log_dest);
    }

}
