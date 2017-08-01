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

use Doctrine\DBAL\Connection;
use ccny\scidiv\cores\model\CoreAccountVO;

/**
 * DAO pattern implementation for persisting CoreAccount
 *
 * @author Daniel Fimiarz/CCNY
 */
class CoreAccountDAO {

    private $connection;

    public function __construct(Connection $conn) {
        $this->connection = $conn;
    }

    /**
     * Retrieves pending accounts from the database
     * 
     * @param Doctrine\DBAL\Connection $conn
     * @return CoreAccountVO[] Array of CoreAccountVO
     */
    public function getPendingAccounts() {

        $pendingAccounts = [];

        $qbuilder = $this->connection->createQueryBuilder();

        $qbuilder->select("u.id", "u.firstname", "u.lastname", "u.phone", "u.email", "u.pi as piid", "concat(p.first_name,' ',p.last_name) as pifullname", "u.username", "u.password", "u.user_type as usertypeid", "cut.short_name as usertype", "u.active_flag as activeflag", "u.last_active as lastactive", "u.note")
                ->from("core_users", "u")
                ->innerJoin("u", "people", "p", "u.pi = p.individual_id")
                ->innerJoin("u", "core_user_types", "cut", "u.user_type = cut.id")
                ->where("u.pi = ?")
                ->setParameter(0, 557, \PDO::PARAM_INT);

        /* @var $stmt ResultCacheStatement|Statement */
        $stmt = $qbuilder->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_OBJ)) {

            //This should be set as a trait
            $pendingAccounts[] = $this->createAccountFromDBRow($row);
        }

        return $pendingAccounts;
    }

    /**
     * 
     * 
     * @param array $row 
     * @return CoreAccountVO
     */
    private function createAccountFromDBRow(\stdClass $row) {

        $account = new CoreAccountVO();

        $account->setId($row->id);
        $account->setFirstname($row->firstname);
        $account->setLastname($row->lastname);
        $account->setPhone($row->phone);
        $account->setEmail($row->email);
        $account->setPiId($row->piid);
        $account->setUsername($row->username);
        $account->setPassword($row->password);
        $account->setUserType($row->usertype);
        $account->setUserTypeId($row->usertypeid);
        $account->setActiveflag($row->activeflag);
        $account->setLastactive($row->lastactive);
        $account->setNote($row->note);
        $account->setPiFullname($row->pifullname);

        return $account;
    }

}