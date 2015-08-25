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

/**
 * Description of CoreEvent
 *
 * @author WORK 1328
 */
class CoreEvent {
    
    
    private $id;
    /* @var $timestamp \DateTime */
    private $timestamp;
    /* @var $start \DateTime */
    protected $start;
    /* @var $end \DateTime */
    protected $end;
    protected $service_id;
    protected $service_state;
    protected $user_id;
    protected $note;
    protected $eventState;
    
    
    public function __construct($id,  \DateTime $timestamp) {
        $this->id = $id;
        $this->timestamp = $timestamp;
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
    
    function getServiceState() {
        return $this->service_state;
    }

    function setServiceState($service_state) {
        $this->service_state = $service_state;
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
    
    public function setEventState($eventState)
    {
        $this->eventState = $eventState;
    }
    
    public function getEventState()
    {
        return $this->eventState;
    }
    
    public function getTimestamp()
    {
        return $this->timestamp;
    }
            
    
    public function isOwner($user_id)
    {
        if ($user_id == $this->user_id) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 
     * @return type int. Number of seconds between event end and start
     */
    public function getDuration()
    {
        $duration = $this->end->getTimestamp() - $this->start->getTimestamp();
	
	return $duration;
    }
    
    /**
     * This function computes temporal state of an event. See TimeStates.php
     */
    public function getTemporalState()
    {
        $now = new \DateTime();
        
        if ($now < $this->start) {
            return TIME_PAST;
        }
        
        if( $now > $this->end)
        {
            return TIME_FUTURE;
        }
        
        return TIME_CURRENT;
    }
    
}
