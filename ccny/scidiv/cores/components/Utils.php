<?php

namespace ccny\scidiv\cores\components;

class Utils {

    // Hold an instance of the class
    private static $instance;

    //A private constructor prevents objects creatation. Singleton
    private function __construct() {
        
    }

    public static function getObject() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    // Prevent users to clone the instance
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    function getRID() {
        $res_id = "";

        if (isset($_GET['RID']) && !empty($_GET['RID'])) {
            $res_id = $_GET['RID'];
        }

        return $res_id;
    }

}

