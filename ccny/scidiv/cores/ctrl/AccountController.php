<?php

namespace ccny\scidiv\cores\ctrl;

use Silex\Application as Application;
use Symfony\Component\HttpFoundation\Request as Request;

use ccny\scidiv\cores\model\CoreAccountDAO;

/*
 * The MIT License
 *
 * Copyright 2017 Daniel Fimiarz <dfimiarz@ccny.cuny.edu>.
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class AccountController {

    public function index(Application $app, Request $request) {
        
        if ($request->attributes->get("is_admin") === true) {
            return "Account home";
        } else {
            return new RedirectResponse($app['url_generator']->generate('login'));
        }
    }

    public function details(Application $app, Request $request, $acc_id) {
        return "Account details for " . $acc_id;
    }

    public function find(Application $app, Request $request) {

        /* @var $session Session */
        $session = $app['session'];

        $query = $request->query->get('q', null);
        $query_type = $request->query->get('qt', null);
        $query_option = $request->query->get('qo', null);


        $errors = [];
        $templ_vars = [];

        if (!is_null($query) && \strlen($query) < 2) {
            $errors[] = "Search query should contain at least 2 charcters";
        }

        $templ_vars['query'] = $query;

        if (!empty($errors)) {
            $templ_vars['errors'] = $errors;
        }

        $accounts = [["uid" => "23", "name" => "Jorge Morales", "uname" => "jmorales", "email" => "jmorales@ccny.cuny.edu", "phone" => "(212) 650-8596"],
            ["uid" => "23", "name" => "Jorge Morales", "uname" => "jmorales", "email" => "jmorales@ccny.cuny.edu", "phone" => "(212) 650-8596"],
            ["uid" => "23", "name" => "Jorge Morales", "uname" => "jmorales", "email" => "jmorales@ccny.cuny.edu", "phone" => "(212) 650-8596"],
        ];

        $templ_vars['accounts'] = $accounts;

        return $app['twig']->render("account/findaccount.html.twig", $templ_vars);
    }
    
    public function listPending(Application $app, Request $request){
        
        $templ_vars = [];
        
        $accountDAO = new CoreAccountDAO($app['db']);
        
        $templ_vars['accounts'] = $accountDAO->getPendingAccounts();
        return $app['twig']->render("account/pendingaccounts.html.twig", $templ_vars);
    }

}
