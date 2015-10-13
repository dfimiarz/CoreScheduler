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

use ccny\scidiv\cores\model\CoreUser as CoreUser;
/**
 * Description of CalendarConfig
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class CalendarConfig {
    
    //Message to display in the mesage window
    public $message;

    //Can this service be used
    public $can_use;

    //Can access to this service be requested
    public $can_request_access;
    
    public function __construct() {
        $this->message = "";
        $this->can_request_access = FALSE;
        $this->can_use = FALSE;
    }
    
    public function toStdClass()
    {
        $config = new \stdClass();
        
        $config->msg = $this->message;
        $config->can_use = $this->can_use;
        $config->can_req = $this->can_request_access;
        
        return $config;
    }
    
   
}
