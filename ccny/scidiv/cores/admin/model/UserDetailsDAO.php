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

namespace ccny\scidiv\cores\admin\model;

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\admin\model\UserDetails as UserDetails;

/**
 * DAO pattern implementation for persisting CoreEventObjects
 *
 * @author Daniel Fimiarz/CCNY
 */
class UserDetailsDAO extends CoreComponent{
    
    private $connection;
    
    public function __construct(\mysqli $connection) {
        parent::__construct();
        $this->connection = $connection;
    }
    
    /** Creates a UserDetails object based on $dec_user_id
     * 
     * @param type $dec_user_id
     * @return UserDetails
     */
    public function getUserDetails($dec_user_id)
    {
        /* @var $details UserDetails */
        $user_details = new UserDetails();

        $query = "SELECT concat(firstname,' ',lastname) as name,c.username,c.last_active, c.phone,c.email, concat(p.first_name,' ',p.last_name) as pi, c.user_type ,c.note FROM core_users c,people p where c.id = ? and p.individual_id = c.pi";
        

        if( ! $stmt = mysqli_prepare($this->connection, $query)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! $stmt->bind_param('i', $dec_user_id)){
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if( ! $stmt->execute()){
            $this->throwDBError($stmt->error, $stmt->errno);
        }
        
        $temp = new \stdClass();
        
        if( ! $stmt->bind_result($temp->name,$temp->username,$temp->lastactive,$temp->phone,$temp->email,$temp->mentor,$temp->type,$temp->note )){
            $this->throwDBError($stmt->error, $stmt->errno);
        }
        
        if ($stmt->fetch()) {
            $user_details->name = $temp->name;
            $user_details->username = $temp->username;
            $user_details->lastactive = $temp->lastactive;
            $user_details->phone = $temp->phone;
            $user_details->email = $temp->email;
            $user_details->mentor = $temp->mentor;
            $user_details->type = $temp->type;
            $user_details->note = $temp->note;
        }

        $stmt->close();
        
        return $user_details;
    }
}
