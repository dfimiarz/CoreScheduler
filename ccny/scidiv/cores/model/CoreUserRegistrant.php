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

use Symfony\Component\HttpFoundation\Request as Request;

/**
 * Description of newUser
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class CoreUserRegistrant {

    public $uname;
    public $psw;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $pi_name;
    public $pi_email;
    public $pi_phone;
    public $pi_address_1;
    public $pi_address_2;
    public $pi_city;
    public $pi_state;
    public $pi_zip;
    
    public function __construct(Request $request) {
        $this->uname = $request->request->get('uname',null);
        $this->psw = $request->request->get('psw1',null);
        $this->email = $request->request->get('email1',null);
        $this->fname = $request->request->get('fname',null);
        $this->lname = $request->request->get('lname',null);
        $this->phone = $request->request->get('phone',null);
        $this->pi_name = $request->request->get('pi_name',null);
        $this->pi_email = $request->request->get('pi_email',null);
        $this->pi_phone = $request->request->get('pi_phone',null);
        $this->pi_address_1 = $request->request->get('pi_address_1',null);
        $this->pi_address_2 = $request->request->get('pi_address_2',null);
        $this->pi_city = $request->request->get('pi_city',null);
        $this->pi_state = $request->request->get('pi_state',null);
        $this->pi_zip = $request->request->get('pi_zip',null);
    }

}
