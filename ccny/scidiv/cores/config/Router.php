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

namespace ccny\scidiv\cores\config;

/**
 * Description of Router
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class Router {

    private $routes = array();

    public function __construct() {
        /*
         * $host and $root vairable should be set to correct values in order for redirect to work as expected
         */
        $host = $_SERVER['HTTP_HOST'];
        $root = 'corescheduler';
        $this->routes['default'] = "http://$host/$root/index.php";
        $this->routes['login'] = "http://$host/$root/login.php";
    }

    public function getDestination($code) {
        if (array_key_exists($code, $this->routes))
            return $this->routes[$code];
        else
            return $this->routes['default'];
    }

}
