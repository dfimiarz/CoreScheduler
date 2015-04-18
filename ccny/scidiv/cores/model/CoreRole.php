<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace ccny\scidiv\cores\model;
/**
 * Description of CoreRole
 *
 * @author WORK 1328
 */
class CoreRole {
    //put your code here
    private $role_id;
    private $role_name;
    
    public function __construct($role_id,$role_name) {
       $this->role_id = $role_id;
       $this->role_name = $role_name;
    }
    
    public function getRoleId()
    {
        return $this->role_id;
    }
    
    public function getRoleName()
    {
        return $this->role_name;
    }
}
