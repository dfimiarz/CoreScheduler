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

/**
 * Description of PermissionToken
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
abstract class PermissionToken {
    /**
     *
     * @var type 2D Array of attributs assigned to this token
     * 
     */
    protected $attribs;
    /**
     *
     * @var type array of attribute names used by a PermissionToken object
     */
    protected $keys;

    public function __construct() {
        $this->attribs = [];
        $this->keys = [];
    }


    public function getTokenAttribs(){
        return $this->attribs;
    }
    
    public function setAttribute($key,$values)
    {
        
        if(is_array($this->attribs) && is_array($this->keys) ){
            if(in_array($key,$this->keys))
            {
                $this->attribs[$key] = $values;
            }
            
        }
    }
    
    public function getAttribute($key)
    {
        if( isset($this->attribs[$key]))
        {
            return $this->attribs[$key];
        }
        
        return [];
    }
    
    public function getJSON()
    {
        if( isset($this->attribs) )
        {
             return json_encode($this->attribs);
        }
        
        return json_encode([]);
        
    }
}
