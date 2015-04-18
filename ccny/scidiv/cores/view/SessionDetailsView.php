<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace ccny\scidiv\cores\view;

require_once __DIR__ . '/../../../../ext/twig/lib/Twig/Autoloader.php';
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

        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates');
        $this->twig_env = new \Twig_Environment($loader, array('debug' => true));
        $this->tmpl = $this->twig_env->loadTemplate('sessionDetails.html.twig');
    }

    public function render($ArrDetails) {

        if (is_array($ArrDetails)) {
            return $this->tmpl->render($ArrDetails);
        } 
           
        return $this->tmpl->render([]);
        
    }

}
