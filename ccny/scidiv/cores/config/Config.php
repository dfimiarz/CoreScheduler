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

namespace ccny\scidiv\cores\config;

class Config
{
    const APP_ICON = "./images/scidivicon.ico";
    const APP_EMAIL = "corelabs@ccny.cuny.edu";
    const APP_NAME = "CCNY CoreLABS";
    const APP_ID = "CORELABS";
    const APP_ENC_KEY = "lENb2bPRk)c&k0ebY0nSxiq9iKgg8WYU";
    
    const RECAPTCHA_PRIV_KEY = "6LfQQ-kSAAAAAFJ64dbozmkTqS89FtEGdc0c1M9r";
    const RECAPTCHA_PUB_KEY = "6LfQQ-kSAAAAAPPUG9Jy31yoSfKyY6N8UQmgqzmB";
    
    private static $AUTHENTICATORS = array( "LDAP"=>"ccny\scidiv\cores\components\auth\LDAPAuth",
                                            "MYSQL"=>"ccny\scidiv\cores\components\auth\MySQLAuth");
    
    /*
     * Number of seconds for minimum session lenght
     */
    const MIN_EVENT_DURATION = 900;
    
    
    /*
     * UNIX '/var/log/corelabs/'
     */
    const LOG_DIR = "C:/weblogs/";
    
    const LOG_FILE = "CoreLABS.log";

    public static function getAuthenticators(){
        return self::$AUTHENTICATORS;
    }
    
}

