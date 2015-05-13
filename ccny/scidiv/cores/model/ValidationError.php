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

include_once __DIR__ . '/../components/SystemConstants.php';

/**
 * Description of ValidationError
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class ValidationError {

    /** @var string Error message */
    private $msg;
    /** @var string Name of the field */
    private $field;
    /** @var int Error code */
    private $code;
    /** @var int Error type. */
    private $type;

    public function __construct() {
        $this->field = "";
        $this->msg = "";
        $this->code = 0;
        $this->type = \VAL_NO_ERROR;
    }
    
    function getMsg() {
        return $this->msg;
    }

    function getField() {
        return $this->field;
    }

    function getCode() {
        return $this->code;
    }

    function getType() {
        return $this->type;
    }

    function setMsg($msg) {
        $this->msg = $msg;
    }

    function setField($field) {
        $this->field = $field;
    }

    function setCode($code) {
        $this->code = $code;
    }

    function setType($type) {
        $this->type = $type;
    }
    
    /**
     * Converts this object info generic stdClass
     * @return \stdClass
     */
    function toStdClass()
    {
        $object = new \stdClass();
        $object->msg = $this->msg;
        $object->field = $this->field;
        $object->code = $this->code;
        $object->type = $this->type;
        
        return $object;
    }


}
