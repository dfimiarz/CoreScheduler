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

use ccny\scidiv\cores\components\Logger as Logger;
use ccny\scidiv\cores\model\ErrorInfo as ErrorInfo;
use ccny\scidiv\cores\components\SystemException as SystemException;

class CoreComponent {


    
    protected function __construct() {
        
        
    }
    
    protected function log($msg, $log_type)
    {
        Logger::log($msg, $log_type);
    }
    
    protected function throwExceptionOnError(ErrorInfo $errinfo) {
        Logger::log($errinfo->getErrMsg(), $errinfo->getLogLvl());
        throw new SystemException($errinfo->getErrMsg(), $errinfo->getErrCode(), $errinfo->getClientErrMsg());       
    }
    
    protected function throwDBError($msg,$code)
    {
        $errinfo = new ErrorInfo($msg, $code, "Crytical database error. Code: " . $code, DATABASE_LOG_TYPE );
        $this->throwExceptionOnError($errinfo);
    }

}
