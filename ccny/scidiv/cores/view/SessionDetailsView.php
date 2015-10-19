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

namespace ccny\scidiv\cores\view;

/**
 * Description of SessionDetails
 *
 * @author WORK 1328
 */
class SessionDetailsView {
    //put your code here
    private $twig_env;
    private $tmpl;
    
    function __construct() {

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates');
        $this->twig_env = new \Twig_Environment($loader, array('debug' => true));
        $this->tmpl = $this->twig_env->loadTemplate('sessionDetails.html.twig');
    }

    public function render($ArrDetails) {

        if (is_array($ArrDetails)) {
            return $this->tmpl->render($ArrDetails);
        } 
           
        return $this->tmpl->render(array());
        
    }

}
