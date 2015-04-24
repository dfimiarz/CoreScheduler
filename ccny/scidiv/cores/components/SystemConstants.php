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


//These are values used by the database to encode each user role in the system
//If changes are made to the database, these values should be corrected here as well
namespace ccny\scidiv\cores\components;

define("DB_NO_ROLE",0);
define("DB_ACCESS_PENDING",1);
define("DB_USER",2);
define("DB_ADMIN",3);

//User roles
define("DB_ROLE_ANONYMOUS",0);
define("DB_ROLE_AUTHENTICATED",500);
define("DB_ROLE_SUSPENDED",1000);
define("DB_ROLE_AUTHORIZED",2000);
define("DB_ROLE_OWNER",2500);
define("DB_ROLE_SERVICE_ADMIN",3000);
define("DB_ROLE_SYSTEM_ADMIN",4000);

//Service states
define("SERVICE_STATE_NOT_ACTIVE",0);
define("SERVICE_STATE_LOCKED",1);
define("SERVICE_STATE_ACTIVE",2);

//Permissions 
define("DB_PERM_VIEW_EVENT",1);
define("DB_PERM_CREATE_EVENT",2);
define("DB_PERM_DELETE_EVENT",3);
define("DB_PERM_EDIT_EVENT",4);
define("DB_PERM_VIEW_DETAILS",5);
define("DB_PERM_EDIT_PAST_EVENT",6);
define("DB_PERM_CHANGE_NOTE",7);
define("DB_PERM_CHANGE_OWNER",8);
define("DB_PERM_REQUEST_ACCESS",9);

//These are types of log types to use in the system
define("DATABASE_LOG_TYPE",0);
define("WARNING_LOG_TYPE",1);
define("SECURITY_LOG_TYPE",2);
define("ERROR_LOG_TYPE",3);
define("ACTIVITY_LOG_TYPE",4);

?>