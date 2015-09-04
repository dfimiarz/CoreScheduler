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
 * Description of CoreEvent
 *
 * @author WORK 1328
 */
class CoreService {
    
    
    private $id;
    private $res_id;
    private $type;
    private $name;
    private $short_name;
    private $state;
    private $description;
    
    
    public function __construct($id) {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    function getResId() {
        return $this->res_id;
    }

    function getType() {
        return $this->type;
    }

    function getName() {
        return $this->name;
    }

    function getShortName() {
        return $this->short_name;
    }

    function getState() {
        return $this->state;
    }

    function getDescription() {
        return $this->description;
    }

    function setResId($resouce_id) {
        $this->resouce_id = $res_id;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setShortName($short_name) {
        $this->short_name = $short_name;
    }

    function setState($state) {
        $this->state = $state;
    }

    function setDescription($description) {
        $this->description = $description;
    }   
}
