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

namespace ccny\scidiv\cores\admin\ctrl;

use ccny\scidiv\cores\components\CoreComponent as CoreComponent;
use ccny\scidiv\cores\model\CoreUser as CoreUser;
use ccny\scidiv\cores\components\DbConnectInfo as DbConnectInfo;
use ccny\scidiv\cores\admin\model\UserDetailsDAO as UserDetailsDAO;
use ccny\scidiv\cores\admin\model\UserDetails as UserDetails;
use ccny\scidiv\cores\components\CryptoManager as CryptoManager;

/**
 * Description of UserDetailsCtrl
 *
 * @author Daniel Fimiarz <dfimiarz@ccny.cuny.edu>
 */
class UserDetailsCtrl extends CoreComponent {
    
    private $connection;
    private $user;
    private $pm;
    private $ud_dao;
   
    
    public function __construct(CoreUser $core_user) {

        parent::__construct();

        $this->user = $core_user;

        $dbinfo = DbConnectInfo::getDBConnectInfoObject();

        @$this->connection = new \mysqli($dbinfo->getServer(), $dbinfo->getUserName(), $dbinfo->getPassword(), $dbinfo->getDatabaseName(), $dbinfo->getPort());

        if ($this->connection->connect_errno) {
            $this->throwDBError($this->connection->connect_error, $this->connection->connect_errno);
        }

        $this->ud_dao = new UserDetailsDAO($this->connection);
        
        
    }
    
    public function getUserDetails($enc_user_id)
    {
        $crypto_mngr = new CryptoManager();
        $dec_user_id = $crypto_mngr->decrypt($enc_user_id);
        
        //Get and return UserDetails object
        try {
            return $this->ud_dao->getUserDetails($dec_user_id);
        } catch (\Exception $ex) {
            return null;
        }
        
        
    }
    
}
