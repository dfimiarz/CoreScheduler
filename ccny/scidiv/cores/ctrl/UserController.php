<?php
namespace ccny\scidiv\cores\ctrl;

use Silex\Application as Application;
use Symfony\Component\HttpFoundation\Request as Request;

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

class UserController{
    
    public function loginAction(Application $app, Request $request){
        
        /* @var $session Session */
        $session = $app['session'];
        $errors = $session->getFlashBag()->get('login_error');
        
        
        $templ_vars = [];
        
        if( ! empty($errors )){
            $templ_vars['errors'] = $errors;
        }
        
        
        return $app['twig']->render("user/login.html.twig",$templ_vars);
    }
    
    public function doLoginAction(Application $app, Request $request){
        $username = $request->request->get('username', '');
        $password = $request->request->get('password', '');
        
        /* @var $session Session */
        $session = $app['session'];
        
        if(! (strcmp($username, 'test') == 0 && strcmp($password,'test') == 0)){
            $session->getFlashBag()->add('login_error','Incorrect username or password');
            return $app->redirect('/login');
        }
        else{
            return $app->redirect('/');
        }
    }
    
    public function findAccountAction(Application $app, Request $request){
        
        /* @var $session Session */
        $session = $app['session'];
        
        $query = $request->query->get('q', null);
        $query_type = $request->query->get('qt', null);
        $query_option = $request->query->get('qo', null );
        
        
        $errors = [];
        $templ_vars = [];
        
        if( !is_null($query) && \strlen($query)  < 2 ){
            $errors[] = "Search query should contain at least 2 charcters";
        }
        
        $templ_vars['query'] = $query;
        
        if( ! empty($errors )){
            $templ_vars['errors'] = $errors;
        }
        
        $accounts = [  ["uid"=>"23","name"=>"Jorge Morales","uname"=>"jmorales","email"=>"jmorales@ccny.cuny.edu","phone"=>"(212) 650-8596"],
                    ["uid"=>"23","name"=>"Jorge Morales","uname"=>"jmorales","email"=>"jmorales@ccny.cuny.edu","phone"=>"(212) 650-8596"],
                    ["uid"=>"23","name"=>"Jorge Morales","uname"=>"jmorales","email"=>"jmorales@ccny.cuny.edu","phone"=>"(212) 650-8596"],
            
                 ];        
        
        $templ_vars['accounts'] = $accounts;
        
        return $app['twig']->render("user/findaccount.html.twig",$templ_vars);
    }
     
}

