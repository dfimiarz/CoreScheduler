<?php

/*
  Class used to send messages to the client
 */

namespace ccny\scidiv\cores\view;

include_once __DIR__ . '/../components/SystemConstants.php';
include_once 'MessageSender.php';

use Symfony\Component\HttpFoundation\Response;

class JSONMessageSender extends MessageSender {

    public function __construct() {
        parent::__construct();
        $this->response->headers->set('Content-Type', 'application/json');
    }

    public function sendMessage($data) {


        $this->response->setContent(\json_encode($data));
        $this->response->setStatusCode(Response::HTTP_OK);


        $this->response->send();
        exit();
    }

}
