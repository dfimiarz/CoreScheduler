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

/**
 * Base class for all system components
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
