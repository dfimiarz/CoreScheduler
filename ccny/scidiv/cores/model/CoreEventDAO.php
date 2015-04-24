<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
    public function getCoreEvent($dec_record_id)
    {
        /* @var $event CoreEvent */
        $event = new CoreEvent($dec_record_id);
        
         //---Get session details
        $query = "SELECT cta.start,cta.end,cta.user,cta.service_id,cta.state as eventstate,cs.state servicestate FROM core_timed_activity cta,core_services cs WHERE cta.id = ? and cta.service_id = cs.id";

        if( ! $stmt = mysqli_prepare($this->connection, $query)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'i', $dec_record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        $temp = new \stdClass();
        
        if( ! mysqli_stmt_bind_result($stmt, $temp->start, $temp->end, $temp->user_id, $temp->service_id,$temp->event_state, $temp->service_state)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }
        
        if (mysqli_stmt_fetch($stmt)) {
            $event->setStart(new \DateTime($temp->start));
            $event->setEnd(new \DateTime($temp->end));
            $event->setServiceId($temp->service_id);
            $event->setUserId($temp->user_id);
            $event->setServiceState($temp->service_state);
            $event->setEventState($temp->event_state);
        }

        mysqli_stmt_close($stmt);
        
        return $event;
    }
    
     /** Saves the event in the database
     * 
     * @param CoreEvent $event
     */
    public function saveCoreEvent(CoreEvent $event)
    {
        $user_id = $event->getUserId();
        $note = $event->getNote();
        $start = $event->getStart();
        $end = $event->getEnd();
        $state = $event->getEventState();
        $record_id = $event->getId();
        
        $start_str = $start->format('Y-m-d H:i:s');
        $end_str = $end->format('Y-m-d H:i:s');
        
        $change_user_q = "UPDATE core_timed_activity SET user = ?,note = ?,start = ?,end = ?,state = ? WHERE id = ?";

        if( ! $stmt = mysqli_prepare($this->connection, $change_user_q)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_bind_param($stmt, 'isssii', $user_id, $note, $start_str, $end_str ,$state ,$record_id)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        if( ! mysqli_stmt_execute($stmt)){
            $this->throwDBError($this->connection->error, $this->connection->errno);
        }

        mysqli_stmt_close($stmt);
    }
}
