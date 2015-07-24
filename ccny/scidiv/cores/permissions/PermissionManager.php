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

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
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
    private $perm_criteria = [];
    
    public function __construct($permission_id) {
        $this->loadCriteria($permission_id);
        
    }
    
    public function checkPermission(PermissionToken $token)
    {
        $perm_result = true;
        
        //Look through perm_criteria array
        foreach ($this->perm_criteria as $key => $carray) {
            //For each perm criteria get criteria's name and values
            foreach ($carray as $attrib_name => $attrib_array) {
                //Compare attributes from criteria with attributes from the token
                $result = array_intersect($attrib_array, $token->getAttribute($attrib_name));
                if (!count($result)) {
                    $perm_result = false;
                    break 2;
                }
            }
        }
        
        return $perm_result;
    }
    
    private function loadCriteria($permission_id)
    {
        $this->perm_criteria[] = array("user_roles"=>[1,2,4,5],"service_states"=>[1,2,3,4],"event_states"=>[1,2]);
        $this->perm_criteria[] = array("user_roles"=>[1,2],"service_states"=>[1],"event_states"=>[2]);
        $this->perm_criteria[] = array("user_roles"=>[1,2,4,5,6],"service_states"=>[2],"event_states"=>[1,2]);
        
        
    }
    
}
