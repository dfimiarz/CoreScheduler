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
namespace ccny\scidiv\cores\config;

/*
 * USER ROLES CODES
 */
define("SYS_ROLE_ANONYMOUS",0);
define("SYS_ROLE_AUTHENTICATED",1);

//Static. Fetched from the database during login
define("ROLE_SUSPENDED",1000);
define("ROLE_AUTHORIZED",2000);
define("ROLE_SERVICE_ADMIN",3000);
define("ROLE_SYSTEM_ADMIN",4000);

//Dynamic roles assigned at runtime
define("EVENT_ROLE_OWNER",1);
define("EVENT_NO_ROLE",0);

/*
 * SERVICE STATE CODES
 */
define("SERVICE_STATE_NOT_ACTIVE",0);
define("SERVICE_STATE_LOCKED",1);
define("SERVICE_STATE_ACTIVE",2);

/*
 * EVENT TIME STATE CODES
 */
define("TIME_FUTURE",0);
define("TIME_CURRENT",1);
define("TIME_PAST",2);

/*
 * PERMISSIONS TYPE CODES
 */
define("PERM_VIEW_EVENT",1);
define("PERM_CREATE_EVENT",2);
define("PERM_DELETE_EVENT",3);
define("PERM_EDIT_EVENT_START",41);
define("PERM_EDIT_EVENT_DURATION",42);
define("PERM_VIEW_DETAILS",5);
define("PERM_CHANGE_NOTE",7);
define("PERM_CHANGE_OWNER",8);
define("PERM_MANAGE_USERS", 10);
define("PERM_ACCESS_SERVICE",11);
define("PERM_REQ_SERVICE_ACCESS",12);

/*
 * LOG TYPE CODES
 */
define("DATABASE_LOG_TYPE",0);
define("WARNING_LOG_TYPE",1);
define("SECURITY_LOG_TYPE",2);
define("ERROR_LOG_TYPE",3);
define("ACTIVITY_LOG_TYPE",4);

/*
 * VALIDATION ERROR CODES
 */
define("VAL_NO_ERROR", 0);
define("VAL_FIELD_ERROR", 1);
define("VAL_SYSTEM_ERROR", 2);
