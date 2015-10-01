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

namespace ccny\scidiv\cores\permissions;

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\permissions\PermissionToken as PermissionToken;
/**
 * Description of PermissionManager
 * 
 * The class checks if a given permission token satisfies permission
 * criteria.
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class PermissionManager extends CoreComponent{
    /**
     *
     * @var type $auth_criteria stores an array of criteria for each permission
     * Format $auth_criteria[$perm_id] = [];
     */
    private $auth_criteria = [];
    
    /*
     * mysqli connecttion object
     */
    private $mysqli;
    
    public function __construct(\mysqli $mysqli) {
        parent::__construct();
        $this->mysqli = $mysqli;     
    }
    
    public function checkPermission($permission_id,PermissionToken $token)
    {
        $this->loadCriteria($permission_id);
        
        $is_authorized = FALSE;
        
        /*
         * Make sure that criteria for a given permission are defined
         */
        if(! isset($this->auth_criteria[$permission_id]))
        {
            $this->auth_criteria[$permission_id] = [];
        }
        
        //Loop through $auth_criteria 
        foreach ($this->auth_criteria[$permission_id] as $criteria) {
            //Evaluate each set of permission criteria agains the token
            $is_authorized = TRUE;
            foreach ($criteria as $criterium_name => $criterium_values) {
                //Compare attributes from criteria with attributes from the token
                $token_attr_values = $token->getAttribute($criterium_name);
                
                $result = array_intersect($criterium_values, $token_attr_values);
                
                if (!count($result)) {
                    //If there is no intersection, move to the next criteria
                    $is_authorized = FALSE;
                    break;
                }
            }
            //If at this point perm result is TURE, auth success
            if( $is_authorized ){
                return $is_authorized;
            }
        }
        
        return $is_authorized;
    }
    
    private function loadCriteria($permission_id)
    {
        //Check if criteria is already loaded for this $permission_id
        if( isset( $this->auth_criteria[$permission_id] )){
            //echo "Criteria found in cache";
            return;
        }

        //Get all user roles and permissions for a given state
        $permission_q = "SELECT attribs FROM core_perm_abac WHERE perm_id = ?";

        if (!$stmt = $this->mysqli->prepare($permission_q)) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!$stmt->bind_param('i', $permission_id)) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if (!$stmt->store_result()) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        $attr_string = "";
        
        $stmt->bind_result($attr_string);

        while ($stmt->fetch()) {     
            $json_obj = json_decode($attr_string, true);
            
            if( !is_null($json_obj))
            {
                $this->auth_criteria[$permission_id][] = $json_obj; 
            }  
        }

        $stmt->free_result();
        $stmt->close();               
        
       
    }
    
}
