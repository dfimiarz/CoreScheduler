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

/*
  Class used to send messages to the client
 */

namespace ccny\scidiv\cores\view;

include_once __DIR__ . '/../autoloader.php';
include_once __DIR__ . '/../components/SystemConstants.php';

use Symfony\Component\HttpFoundation\Response as Response;

interface iMessageSender {

    function sendMessage($response);
}

class MessageSender implements iMessageSender {
    

    /* @var $response Symfony\Component\HttpFoundation\Response */
    protected $response;

    public function __construct() {
        $this->response = new Response();
        $this->response->headers->set('Content-Type', 'text/html');
    }

    public function onError($data = null, $msg, $err_type = 1) {
        $response = new \stdClass();

        $response->error = $err_type;
        $response->message = $msg;
        $response->data = $data;

        $this->sendMessage($response);
    }

    public function onResult($data = null, $msg) {
        $response = new \stdClass();

        $response->error = 0;
        $response->message = "OK";
        $response->data = $data;

        $this->sendMessage($response);
    }

    public function sendMessage($data) {
        
        $this->response->setContent(print_r($data));
        $this->response->setStatusCode(Response::HTTP_OK);
       

        $this->response->send();
        exit();
    }

}