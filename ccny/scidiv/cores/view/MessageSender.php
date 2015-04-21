<?php

/*
  Class used to send messages to the client
 */

namespace ccny\scidiv\cores\view;

include_once __DIR__ . '/../autoloader.php';
include_once __DIR__ . '/../components/SystemConstants.php';

use Symfony\Component\HttpFoundation\Response;

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