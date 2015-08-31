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
use ccny\scidiv\cores\model\CoreEventDetails as CoreEventDetails;

/**
 * DAO pattern implementation for persisting CoreEventObjects
 *
 * @author Daniel Fimiarz/CCNY
 */
class CoreEventDetailsDAO extends CoreComponent{
    
    private $connection;
    
    public function __construct(\mysqli $connection) {
        parent::__construct();
        $this->connection = $connection;
    }
    
    /** Creates a CoreEvent object based on $dec_record_id
     * 
     * @param type $dec_record_id
     * @return CoreEvent
     */
    public function getCoreEventDetails($dec_record_id,\DateTime $timestamp)
    {
        /* @var $details CoreEventDetails */
        $details = null;
        $timestamp_str = $timestamp->format('Y-m-d H:i:s');
        
         //---Get session details
        $query = "SELECT * FROM core_event_details_view WHERE record_id = ? and timestamp = ?";

        if( ! $stmt = mysqli_prepare($this->connection, $query)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'is', $dec_record_id,$timestamp_str)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();
        
        if( ! mysqli_stmt_bind_result($stmt,$temp->record_id,$temp->user_id,$temp->firstname,$temp->lastname,$temp->username,$temp->email,$temp->piname,$temp->timestamp,$temp->start,$temp->end,$temp->note,$temp->event_state,$temp->service_id,$temp->service_name,$temp->service_state,$temp->resource_name )){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        
        if (mysqli_stmt_fetch($stmt)) {
            
            $details = $this->makeEventDetails($temp);
                   
        }

        mysqli_stmt_close($stmt);
        
        return $details;
    }
    
    public function getEventDetailsForTimeRange(\DateTime $start,\DateTime $end,$resource_id)
    {
        //Get all sessions for given service and time range
        $query = "SELECT * FROM core_event_details_view WHERE start <= ? AND end >= ? AND service_id IN (SELECT id from core_services WHERE resource_id = ? ) AND event_state = 1";

        if (!$stmt = $this->connection->prepare($query)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $start_time_str = $start->format(\DATE_RFC3339);
        $end_time_str = $end->format(\DATE_RFC3339);

        if (!$stmt->bind_param('ssi', $end_time_str, $start_time_str, $resource_id)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();

        if( ! mysqli_stmt_bind_result($stmt,$temp->record_id,$temp->user_id,$temp->firstname,$temp->lastname,$temp->username,$temp->email,$temp->piname,$temp->timestamp,$temp->start,$temp->end,$temp->note,$temp->event_state,$temp->service_id,$temp->service_name,$temp->service_state,$temp->resource_name )){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        } 
        
        while ($stmt->fetch()) {  
            $temp_event_array[] = $this->makeEventDetails($temp);
        }

        $stmt->close();
        
        return $temp_event_array;
    }
    
    private function makeEventDetails($temp) {

        $details = new CoreEventDetails($temp->record_id, new \DateTime($temp->timestamp));

        $details->setStart(new \DateTime($temp->start));
        $details->setEnd(new \DateTime($temp->end));
        $details->setServiceId($temp->service_id);
        $details->setUserId($temp->user_id);
        $details->setEventState($temp->event_state);
        $details->setNote($temp->note);
        $details->setFirstname($temp->firstname);
        $details->setLastname($temp->lastname);
        $details->setUsername($temp->username);
        $details->setEmail($temp->email);
        $details->setPiname($temp->piname);
        $details->setService($temp->service_name);
        $details->setResource($temp->resource_name);
        $details->setServiceState($temp->service_state);

        return $details;
    }

}
