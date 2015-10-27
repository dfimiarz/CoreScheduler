<?php

/*
 * The MIT License
 *
 * Copyright 2015 Daniel F.
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
 * Description of SystemExceptionDetails
 *
 * @author Daniel F
 */
class SystemExceptionDetails {
    private $code;
    private $user_msg;
    private $system_msg;
    
    public function __construct($code,$user_msg,$system_msg) {
        $this->code = $code;
        $this->user_msg = $user_msg;
        $this->system_msg = $system_msg;
        
    }
    
    function getCode() {
        return $this->code;
    }

    function getUserMsg() {
        return $this->user_msg;
    }

    function getSystemMsg() {
        return $this->system_msg;
    }

    function setCode($code) {
        $this->code = $code;
    }

    function setUserMsg($user_msg) {
        $this->user_msg = $user_msg;
    }

    function setSystemMsg($system_msg) {
        $this->system_msg = $system_msg;
    }


}
