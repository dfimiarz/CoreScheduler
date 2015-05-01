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
use ccny\scidiv\cores\model\CoreEvent as CoreEvent;

/**
 * DAO pattern implementation for persisting CoreEventObjects
 *
 * @author Daniel Fimiarz/CCNY
 */
class CoreEventDAO extends CoreComponent{
    
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
    public function getCoreEvent($dec_record_id,\DateTime $timestamp)
    {
        /* @var $event CoreEvent */
        $event = null;
        $timestamp_str = $timestamp->format('Y-m-d H:i:s');
        
         //---Get session details
        $query = "SELECT cta.start,cta.end,cta.user,cta.service_id,cta.note,cta.state as eventstate,cta.time_modified FROM core_timed_activity cta WHERE cta.id = ? AND cta.time_modified = ?";

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
        
        if( ! mysqli_stmt_bind_result($stmt, $temp->start, $temp->end, $temp->user_id, $temp->service_id,$temp->note,$temp->event_state,$temp->time_modified)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        
        if (mysqli_stmt_fetch($stmt)) {
            
            $event = new CoreEvent($dec_record_id,new \DateTime($temp->time_modified));
            
            $event->setStart(new \DateTime($temp->start));
            $event->setEnd(new \DateTime($temp->end));
            $event->setServiceId($temp->service_id);
            $event->setUserId($temp->user_id);
            $event->setEventState($temp->event_state);
            $event->setNote($temp->note);
        }

        mysqli_stmt_close($stmt);
        
        return $event;
    }
    
    /** Saves CoreEvent to the database.
     * 
     * @param CoreEvent $event
     * @return boolean
     */ 
    public function saveCoreEvent(CoreEvent $event)
    {
        $user_id = $event->getUserId();
        $note = $event->getNote();
        $start = $event->getStart();
        $end = $event->getEnd();
        $state = $event->getEventState();
        $record_id = $event->getId();
        $timestamp = $event->getTimestamp();
        $note = $event->getNote();
        
        $start_str = $start->format('Y-m-d H:i:s');
        $end_str = $end->format('Y-m-d H:i:s');
        $timestamp_str = $timestamp->format('Y-m-d H:i:s');
        
        $change_user_q = "UPDATE core_timed_activity SET user = ?,note = ?,start = ?,end = ?,state = ?, note = ? WHERE id = ? AND time_modified = ?";

        if( ! $stmt = mysqli_prepare($this->connection, $change_user_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'isssisis', $user_id, $note, $start_str, $end_str ,$state ,$note ,$record_id,$timestamp_str)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $rows = $stmt->affected_rows;
        
        mysqli_stmt_close($stmt);
        
        if ($rows != 1) {
            return false;
        }

        return true;
    }
}
