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
class CoreEventDAO extends CoreComponent {

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
    public function getCoreEvent($dec_record_id, \DateTime $timestamp) {
        /* @var $event CoreEvent */
        $event = null;
        $timestamp_str = $timestamp->format('Y-m-d H:i:s');

        //---Get session details
        $query = "SELECT cta.start,cta.end,cta.user,cta.service_id,cta.note,cta.state as eventstate,cta.time_modified FROM core_timed_activity cta WHERE cta.id = ? AND cta.time_modified = ?";

        if (!$stmt = mysqli_prepare($this->connection, $query)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_bind_param($stmt, 'is', $dec_record_id, $timestamp_str)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_execute($stmt)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();

        if (!mysqli_stmt_bind_result($stmt, $temp->start, $temp->end, $temp->user_id, $temp->service_id, $temp->note, $temp->event_state, $temp->time_modified)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (mysqli_stmt_fetch($stmt)) {

            $event = new CoreEvent($dec_record_id, new \DateTime($temp->time_modified));

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
    public function saveCoreEvent(CoreEvent $event) {
        $user_id = $event->getUserId();
        $note = $event->getNote();
        $start = $event->getStart();
        $end = $event->getEnd();
        $state = $event->getEventState();
        $record_id = $event->getId();
        $timestamp = $event->getTimestamp();
        $note = $event->getNote();

        $start_str = $start->format(\DATE_RFC3339);
        $end_str = $end->format(\DATE_RFC3339);
        $timestamp_str = $timestamp->format(\DATE_RFC3339);

        $change_user_q = "UPDATE core_timed_activity SET user = ?,note = ?,start = ?,end = ?,state = ?, note = ? WHERE id = ? AND time_modified = ?";

        if (!$stmt = mysqli_prepare($this->connection, $change_user_q)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_bind_param($stmt, 'isssisis', $user_id, $note, $start_str, $end_str, $state, $note, $record_id, $timestamp_str)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!mysqli_stmt_execute($stmt)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $rows = $stmt->affected_rows;

        mysqli_stmt_close($stmt);

        if ($rows != 1) {
            return false;
        }

        return true;
    }

    private function lockTables() {
        //Lock tables
        $lock_q = "LOCK TABLES core_timed_activity WRITE, core_services AS cs1 READ, core_services AS cs2 READ";

        if (!$this->connection->query($lock_q)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
    }

    private function unlockTables() {
        //Unlock tables
        $unlock_q = "UNLOCK TABLES";

        if (!$this->connection->query($unlock_q)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
    }

    private function isTimeslotAvailable(CoreEvent $event) {
        
        $update_check_q = "SELECT IF( COUNT(1),0,1 ) AS Available FROM core_timed_activity WHERE service_id in (SELECT id FROM core_services AS cs1 WHERE resource_id = (SELECT resource_id FROM core_services AS cs2 WHERE id = ?)) AND state = 1 AND start < ? AND end > ? AND id <> ?";
        $insert_check_q = "SELECT IF( COUNT(1),0,1 ) AS Available FROM core_timed_activity WHERE service_id in (SELECT id FROM core_services AS cs1 WHERE resource_id = (SELECT resource_id FROM core_services AS cs2 WHERE id = ?)) AND state = 1 AND start < ? AND end > ?"; 
        
        $check_q = "";
        
        /*
         * Choose a query to run depending on the status of the event ID
         * Event id = null signifies a new event.
         */
        if (is_null($event->getId())) {
            $check_q = $insert_check_q;
        } else {
            $check_q = $update_check_q;
        }

        $new_start_time_str = $event->getStart()->format(\DATE_RFC3339);
        $new_end_time_str = $event->getEnd()->format(\DATE_RFC3339);

        $service_id = $event->getServiceId();
        $event_id = $event->getId();
        
        if (!$stmt = $this->connection->prepare($check_q)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        
        /*
         * Number of parameters changes based on the type of query used
         */
        if (is_null($event->getId())) {
            $result = $stmt->bind_param('iss', $service_id, $new_end_time_str, $new_start_time_str);
        } else {
            $result = $stmt->bind_param('issi', $service_id, $new_end_time_str, $new_start_time_str, $event_id);
        }
        
        if (!$result) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }


        if (!$stmt->execute()) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $available = 0;

        if (!$stmt->bind_result($available)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $stmt->fetch();

        $stmt->free_result();
        $stmt->close();

        return $available;
    }

    /** Modifies event time. The function checks for new time availibility
     * 
     * @param CoreEvent $event
     */
    public function modifyEventTime(CoreEvent $event) {
        $this->lockTables();

        if (!$this->isTimeslotAvailable($event)) {
            $this->throwExceptionOnError("Events cannot overlap", 0, \ACTIVITY_LOG_TYPE);
        }

        if (!$this->saveCoreEvent($event)) {
            $this->throwExceptionOnError("Could not modify the event", 0, \ERROR_LOG_TYPE);
        }

        $this->unlockTables();
    }

    private function createCoreEvent(CoreEvent $event) {
        
        $insert_q = "INSERT INTO `core_timed_activity` (`id`,`service_id`,`time_recorded`,`time_modified`,`state`,`start`,`end`,`user`,`note`) VALUES (null,?,	NOW(),NOW(),?,?,?,?,?)";

        if (!$stmt = $this->connection->prepare($insert_q)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $service_id = $event->getServiceId();
        $state = $event->getEventState();
        $start_time_str = $event->getStart()->format(\DATE_RFC3339);
        $end_time_str = $event->getEnd()->format(\DATE_RFC3339);
        $user_id = $event->getUserId();
        $note = $event->getNote();
        
        if (!$stmt->bind_param('iissis', $service_id, $state, $start_time_str, $end_time_str, $user_id,$note)) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        
        $record_id = $stmt->insert_id;
        
        $stmt->close();
        
        return $record_id;
    }
    
    public function insertCoreEvent(CoreEvent $event)
    {
        $this->lockTables();
        
        if (!$this->isTimeslotAvailable($event)) {
            $this->throwExceptionOnError("Events cannot overlap", 0, \ACTIVITY_LOG_TYPE);
        }

        $new_id = $this->createCoreEvent($event);
        
        if (! $new_id ) {
            $this->throwExceptionOnError("Could not add this event", 0, \ERROR_LOG_TYPE);
        }
        
        $this->unlockTables();
        
        return $new_id;
    }
    
    /**
     * The function check for existance of an adjecent event.
     * @param CoreEvent $new_event
     * @return CoreEvent
     */
    public function getAdjacentEvent(CoreEvent $new_event) {

        $adj_event = null;
        
        $user_id = $new_event->getUserId(); 
        $service_id = $new_event->getServiceId();
        $new_start_time_str  = $new_event->getStart()->format(\DATE_RFC3339);
        $new_end_time_str = $new_event->getEnd()->format(\DATE_RFC3339);
        
        $adjcent_check_q = "SELECT cta.id,cta.start,cta.end,cta.user,cta.service_id,cta.note,cta.state as eventstate,cta.time_modified FROM core_timed_activity cta WHERE user = ? and service_id = ? and state = 1 and (end = ? OR start = ?) ORDER BY start LIMIT 1";
       
        if( ! $stmt = $this->connection->prepare($adjcent_check_q))
        {
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        
        if( ! $stmt->bind_param('iiss', $user_id, $service_id, $new_start_time_str, $new_end_time_str))
        {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if( ! $stmt->execute())
        {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        $temp = new \stdClass();

        if (! $stmt->bind_result($temp->id,$temp->start, $temp->end, $temp->user_id, $temp->service_id, $temp->note, $temp->event_state, $temp->time_modified)) {
            $this->throwDBError($stmt->error, $stmt->errno);
        }

        if ($stmt->fetch()) {

            $adj_event = new CoreEvent($temp->id, new \DateTime($temp->time_modified));

            $adj_event->setStart(new \DateTime($temp->start));
            $adj_event->setEnd(new \DateTime($temp->end));
            $adj_event->setServiceId($temp->service_id);
            $adj_event->setUserId($temp->user_id);
            $adj_event->setEventState($temp->event_state);
            $adj_event->setNote($temp->note);
        }

        $stmt->free_result();
        $stmt->close();
       
        return $adj_event;
    }

}
