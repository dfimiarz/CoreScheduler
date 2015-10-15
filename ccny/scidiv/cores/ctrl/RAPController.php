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

namespace ccny\scidiv\cores\ctrl;

use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session as Session;
use ccny\scidiv\cores\components\Router as Router;

/**
 * Base class implementation of Redirect After Post controller
 * $dest_code and $dest_params will be used to build the redirect URL
 *
 * @author Daniel F
 */
abstract class RAPController {
  
    /** @var Request */
    protected $request;
    /** @var Session */
    protected $session;
    /** @var Router */
    protected $router;
    /** @var String */
    protected $dest_code = 'default';
     /** @var String */
    protected $dest_params = '';
    
    public function __construct() {
        
        $this->request = Request::createFromGlobals();
        
        $this->session = new Session();
        $this->session->start();
        
        $this->router = new Router();
        
        /*
         * Check if $_POST['dest'] is set. If so, save the code in the controller
         * for future redirect
         */
        $dest = $this->request->request->get("dest", null);
        if (!is_null($dest)) {
            $this->dest_code = $dest;
        }
        
    }

    abstract public function run();

    protected function redirect(){
        $dest_url = $this->router->getDestination($this->dest_code);
        
        $url = $dest_url . $this->dest_params;
        
        $response = new RedirectResponse($url);
        $response->send(); 
        
        exit();
    }
    
    abstract protected function success();
    abstract protected function failure($message);
    

}
