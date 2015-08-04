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

include_once __DIR__ . '/../components/CoreComponent.php';
include_once __DIR__ . '/PermissionToken.php';

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
    //put your code here
    private $auth_criteria = [];
    
    public function __construct($permission_id) {
        $this->loadCriteria($permission_id);
        
    }
    
    public function checkPermission(PermissionToken $token)
    {
        $is_authorized = TRUE;
        
        //Loop through $auth_criteria 
        foreach ($this->auth_criteria as $criteria) {
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
        
        $json_txt[] = '{"user_roles":[1,2],"service_states":[1,2,3],"event_states":[1,2,3]}';
        $json_txt[] = '{"user_roles":[1],"service_states":[1,2,3],"event_states":[1]}';
        $json_txt[] = '{"user_roles":[4],"service_states":[1,2,3],"event_states":[1,2,3,4]}';
        
        foreach ($json_txt as $key => $value) {
            $this->auth_criteria[]= json_decode($value, true); 
        }
        
                
        
    }
    
}
