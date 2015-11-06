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

namespace ccny\scidiv\cores\model;

/**
 * Description of CoreErrorDetails
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class CoreErrorDetails {
    
    private $sys_err_msg;
    private $sys_err_code;
    
    private $ui_err_msg;
    private $ui_err_code;

    private $err_type;

    public function __construct($sys_err_msg,$sys_err_code,$error_type = ERROR_LOG_TYPE,$ui_err_msg = null,$ui_err_code = null) {
        
        $this->sys_err_code = $sys_err_code;
        $this->sys_err_msg = $sys_err_msg;
        $this->ui_err_code = $ui_err_code;
        $this->ui_err_msg = $ui_err_msg;
        $this->err_type = $error_type;
    
        
    }
    
    function getSysErrMsg() {
        return $this->sys_err_msg;
    }

    function getSysErrCode() {
        return $this->sys_err_code;
    }

    function getUIErrMsg() {
        return $this->ui_err_msg;
    }

    function getUIErrCode() {
        return $this->ui_err_code;
    }

    function getErrType() {
        return $this->err_type;
    }




}
