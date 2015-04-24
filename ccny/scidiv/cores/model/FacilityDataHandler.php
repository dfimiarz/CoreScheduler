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

include_once __DIR__ . '/../components/DbConnectInfo.php';
include_once __DIR__ . '/../components/SystemConstants.php';
include_once __DIR__ . '/../components/CoreComponent.php';

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;

class FacilityDataHandler extends CoreComponent {

    private $mysqli;
    private $stateInfo = null;
    private $time_requested = null;
    private $key = "This is a test";

    //Class constructor
    function __construct() {
        parent::__construct();

        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        @$this->mysqli = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($this->mysqli->connect_errno) {
            $this->throwDBError($this->mysqli->connect_error, $this->mysqli->connect_errno);
        }
    }

    function __destruct() {

       $this->mysqli->close();
    }

    function getFacilities() {


        $result_array = array();

        /*
          10-18-2013| Modify facility selection to pull only facilities with services that are time based. See database structure for details
         */
        
        $query = "SELECT group_id,name FROM groups where group_id in (SELECT distinct(facility_id)  FROM core_resources where id in (select resource_id from core_services where type = 1 and state > 0))";

        if (!$stmt = $this->mysqli->prepare($query)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }
        
        $temp = new \stdClass();

        $stmt->bind_result($temp->group_id, $temp->label);

        while ($stmt->fetch()) {
            $facility = new \stdClass();

            $facility->id = $temp->group_id;
            $facility->label = $temp->label;

            $result_array[] = $facility;
        }

        $stmt->free_result();
        $stmt->close();
        
        return $result_array;
    }

    function getResources($facility_id = null) {

        $result_array = array();

        if ($facility_id == null)
            return $result_array;

        $query = "SELECT id,name FROM core_resources WHERE facility_id = ? AND id in (select resource_id from core_services where type = 1 and state > 0)";

        if (!$stmt = $this->mysqli->prepare($query)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->bind_param('i', $facility_id)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        $temp = new \stdClass();

        $stmt->bind_result($temp->id, $temp->name);

        while ($stmt->fetch()) {
            $resource = new \stdClass();
            $resource->id = $temp->id;
            $resource->label = $temp->name;

            $result_array[] = $resource;
        }

        $stmt->free_result();
        $stmt->close();
                
        return $result_array;
    }

    function getServices($resource_id = null) {

        $result_array = array();

        if ($resource_id == null)
            return $result_array;

        $query = "SELECT id,short_name,state FROM core_services WHERE resource_id = ? and state > 0";

        if (!$stmt = $this->mysqli->prepare($query)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->bind_param('i', $resource_id)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        $temp = new \stdClass();

        $stmt->bind_result($temp->id, $temp->short_name, $temp->state);

        while ($stmt->fetch()) {
            
            $resource = new \stdClass();
            $resource->id = $temp->id;
            $resource->label = $temp->short_name;

            if ($temp->state == \SERVICE_STATE_LOCKED) {
                $resource->label .= " (LOCKED)";
            }

            $result_array[] = $resource;
        }

        $stmt->free_result();
        $stmt->close();
     
        return $result_array;
    }

    function getServiceSelectorContent($resource_name = null) {


        $selector_values = new \stdClass();

        if ($resource_name == null)
            return $result_array;

        $query = "SELECT id,facility_id FROM core_resources WHERE short_name = ? AND id in (select resource_id from core_services where type = 1 and state > 0)";

        if (!$stmt = $this->mysqli->prepare($query)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->bind_param('s', $resource_name)) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }

        if (!$stmt->execute()) {
            $this->throwDBError($this->mysqli->error, $this->mysqli->errno);
        }
        
        
        $temp = new \stdClass();

        $stmt->bind_result($temp->resource_id, $temp->facility_id);    
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $selector_values->ser_array = $this->getServices($temp->resource_id);
        $selector_values->res_array = $this->getResources($temp->facility_id);
        $selector_values->fac_array = $this->getFacilities();

        $selector_values->fac_id = $temp->facility_id;
        $selector_values->res_id = $temp->resource_id;

        return $selector_values;
    }

}
