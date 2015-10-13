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

namespace ccny\scidiv\cores\model;

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\model\CoreService as CoreService;

/**
 * DAO pattern implementation for persisting CoreEventObjects
 *
 * @author Daniel Fimiarz/CCNY
 */
class CoreServiceDAO extends CoreComponent {

    private $connection;

    public function __construct(\mysqli $connection) {
        parent::__construct();
        $this->connection = $connection;
    }

    /** Creates a CoreService object based on $id
     * 
     * @param type $id
     * @return CoreEvent
     */
    public function getCoreService($id) {
        /* @var $service CoreService */
        $service = null;
        
        //---Get session details
        $query = "SELECT id,resource_id as resid,type,name,short_name,state,description FROM core_services cs WHERE cs.id = ?";

        if (!$stmt = mysqli_prepare($this->connection, $query)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_bind_param($stmt, 'i', $id )) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_execute($stmt)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();

        if (!mysqli_stmt_bind_result($stmt, $temp->id, $temp->resid, $temp->type, $temp->name, $temp->short_name, $temp->state, $temp->description)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (mysqli_stmt_fetch($stmt)) {
                
            $service = new CoreService($id);
            $service->setResId($temp->resid);
            $service->setType($temp->type);
            $service->setName($temp->name);
            $service->setShortName($temp->short_name);
            $service->setState($temp->state);
            $service->setDescription($temp->description);
            
        }

        mysqli_stmt_close($stmt);

        return $service;
    }
    
    public function getServiceByEventId($event_id)
    {
        /* @var $service CoreService */
        $service = null;
        
        //---Get session details
        $query = "SELECT id,resource_id as resid,type,name,short_name,state,description FROM core_services cs,core_timed_activity cta WHERE cs.id = cta.service_id AND cta.id = ?";

        if (!$stmt = mysqli_prepare($this->connection, $query)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_bind_param($stmt, 'i', $event_id )) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_execute($stmt)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();

        if (!mysqli_stmt_bind_result($stmt, $temp->id, $temp->resid, $temp->type, $temp->name, $temp->short_name, $temp->state, $temp->description)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (mysqli_stmt_fetch($stmt)) {
                
            $service = new CoreService($id);
            $service->setResId($temp->resid);
            $service->setType($temp->type);
            $service->setName($temp->name);
            $service->setShortName($temp->short_name);
            $service->setState($temp->state);
            $service->setDescription($temp->description);
            
        }

        mysqli_stmt_close($stmt);

        return $service;
    }

}
