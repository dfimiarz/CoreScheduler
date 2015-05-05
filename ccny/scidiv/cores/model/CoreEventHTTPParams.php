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
 * Description of CoreEventHTTPParams
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class CoreEventHTTPParams {

    private $enc_rec_id;
    /* @var $timestamp \DateTime */
    private $timestamp;
    private $dayDelta;
    private $minuteDelta;

    public function __construct() {
        $this->dayDelta = 0;
        $this->minuteDelta = 0;
        $this->enc_rec_id = "0";
        $this->timestamp = new \DateTime();
    }

    public function getEncRecID() {
        return $this->enc_rec_id;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function getDayDelta() {
        return $this->dayDelta;
    }

    public function getMinuteDelta() {
        return $this->minuteDelta;
    }

    public function setEncRecId($encrypted_record_id) {
        $this->enc_rec_id = $encrypted_record_id;
    }

    public function setTimestamp($timestamp) {

        try {
            $this->timestamp = new \DateTime($timestamp);
        } catch (Exception $e) {
            $this->timestamp = new \DateTime();
        }
    }

    public function setDayDelta($dayDelta) {
        $this->dayDelta = \intval($dayDelta);
    }

    public function setMinuteDelta($minuteDelta) {
        $this->minuteDelta = \intval($minuteDelta);
    }

}
