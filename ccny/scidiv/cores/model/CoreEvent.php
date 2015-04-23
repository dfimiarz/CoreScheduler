<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ccny\scidiv\cores\model;

/**
 * Description of CoreEvent
 *
 * @author WORK 1328
 */
class CoreEvent {
    
    
    private $id;
    /* @var $start \DateTime */
    protected $start;
    /* @var $end \DateTime */
    protected $end;
    protected $service_id;
    protected $user_id;
    protected $note;
    protected $serviceState;
    protected $eventState;
    
    public function __construct($id) {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setStart(\DateTime $start)
    {
        $this->start = $start;
    }
    
    public function getStart()
    {
        return $this->start;
    }
    
    public function setEnd(\DateTime $end)
    {
        $this->end = $end;
    }
    
    public function getEnd()
    {
        return $this->end;
    }
    
    public function setServiceId($service_id)
    {
        $this->service_id = $service_id;
    }
    
    public function getServiceId()
    {
        return $this->service_id;
    }
    
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    
    public function getUserId()
    {
        return $this->user_id;
    }
    
    public function setNote($note)
    {
        $this->note = $note;
    }
    
    public function getNote()
    {
        return $this->note;
    }
    
    public function setServiceState($serviceState)
    {
        $this->serviceState = $serviceState;
    }
    
    public function getServiceState()
    {
        return $this->serviceState;
    }
    
    public function setEventState($eventState)
    {
        $this->eventState = $eventState;
    }
    
    public function getEventState()
    {
        return $this->eventState;
    }
    
    public function isOwner($user_id)
    {
        if ($user_id == $this->user_id) {
            return true;
        }
        
        return false;
    }
    
}
