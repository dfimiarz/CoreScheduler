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
class CoreAccountVO {
    
    
    private $id;
    private $firstname;
    private $lastname;
    private $phone;
    private $email;
    private $piId;
    private $piFullname;
    private $username;
    private $password;
    private $userType;
    private $userTypeId;
    private $activeFlag;
    private $lastActive;
    private $note;
    
    function getId() {
        return $this->id;
    }

    function getFirstname() {
        return $this->firstname;
    }

    function getLastname() {
        return $this->lastname;
    }

    function getPhone() {
        return $this->phone;
    }

    function getEmail() {
        return $this->email;
    }

    function getPiId() {
        return $this->piId;
    }

    function getPiFullname() {
        return $this->piFullname;
    }

    function getUsername() {
        return $this->username;
    }

    function getPassword() {
        return $this->password;
    }

    function getUserType() {
        return $this->userType;
    }

    function getActiveFlag() {
        return $this->activeFlag;
    }

    function getLastActive() {
        return $this->lastActive;
    }

    function getNote() {
        return $this->note;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    function setPhone($phone) {
        $this->phone = $phone;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setPiId($piId) {
        $this->piId = $piId;
    }

    function setPiFullname($piFullname) {
        $this->piFullname = $piFullname;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setUserType($userType) {
        $this->userType = $userType;
    }

    function setActiveFlag($activeFlag) {
        $this->activeFlag = $activeFlag;
    }

    function setLastActive($lastActive) {
        $this->lastActive = $lastActive;
    }

    function setNote($note) {
        $this->note = $note;
    }

    function getUserTypeId() {
        return $this->userTypeId;
    }

    function setUserTypeId($userTypeId) {
        $this->userTypeId = $userTypeId;
    }


    
    
}
