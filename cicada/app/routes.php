<?php
/*
 *  Copyright 2013 Christian Grobmeier
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing,
 *  software distributed under the License is distributed
 *  on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
 *  either express or implied. See the License for the specific
 *  language governing permissions and limitations under the License.
 */
use Cicada\Action;
use Cicada\Auth\LoginAction;
use Cicada\Auth\LogoutAction;
use Cicada\Configuration;
use Cicada\Responses\EchoResponse;
use Cicada\Responses\PhpResponse;
use Cicada\Validators\StringLengthValidator;

protect('/\/admin\/.*$/', config('userProvider'))
    ->allowRoles(array("admin"))
    ->allowUsers(array("anna"))
    ->setOnFail(forward('/login'));

get('/\/lazy\/(?<name>.*)$/', 'Cicada\Examples\LazyAction');

get('/\/lazy$/', 'Cicada\Examples\LazyAction')
    ->allowGetField('hello', array( new StringLengthValidator(20) ));

get('/\/puttext$/', function() {
    return new EchoResponse("Hello World");
});
put('/\/puttext$/', 'Cicada\Examples\PutTextAction');
put('/\/puttextclosure$/', 'Cicada\Examples\PutTextClosureAction');

get('/\/hello\/world$/', function() {
    return new EchoResponse("Hello World");
});

get('/\/hello\/(?<name>.*)\/(?<blub>.*)$/', function($name, $blub) {
    return new EchoResponse("Hello Parameter: " .$name . " + " . $blub);
});

get('/\/hello\/(?<name>.*)$/', function($name) {
    return new EchoResponse("Hello Parameter: " .$name);
});

// Allowed GET param test1, test2, test3
get('/\/paramtest$/', function() {
    $request = '';
    $keys = array_keys($_GET);
    foreach ($keys as $key) {
        $request .= $key." -> #". $_GET[$key]."#<br/>".PHP_EOL;
    }
    return new EchoResponse("<h1>Get Parameter</h1>" .PHP_EOL.$request);

})
    ->allowGetField("test1",  array( new StringLengthValidator(3) ))
    ->allowGetField("test2",  array( new StringLengthValidator(3) ))
    ->allowGetField("test3",  array( new StringLengthValidator(3) ));

get('/\/logout$/', function() {
    (new LogoutAction())->execute();
    return new PhpResponse('auth/login.php');
});

get('/\/login$/', function() {
    return new PhpResponse('auth/login.php');
});

post('/\/login\/do$/', function() {
    $username = readPost('username');
    $password = readPost('password');

    $action = new LoginAction($username, $password);
    $action->setUserProvider(config('userProvider'));
    $result = $action->execute();

    if ($result == Action::SUCCESS) {
        $echo = new EchoResponse();
        $echo->addHeader("Location: /admin/dashboard");
        return $echo;
    } else {
        return new PhpResponse('auth/login.php', array("username" => $username));
    }
})
    ->allowPostField("username", array( new StringLengthValidator(20) ))
    ->allowPostField("password", array( new StringLengthValidator(20) ));

get('/\/phptemplate\/decorator$/', function() {
    $response = new PhpResponse('helloworld.php', array( 'name' => "myname", 'ups' => 'huhu'));
    $response->setDecorator('base.php');
    return $response;
});

get('/\/phptemplate\/(?<name>.*)$/', function($name) {
    return new PhpResponse('helloworld.php', array( 'name' => $name, 'ups' => 'huhu'));
});

get('/\/admin\/dashboard$/', function() {
    return new EchoResponse('You are seeing the dashboard. <a href="/logout">Logout</a>');
});

get('/\/admin/', forward('/admin/dashboard'));
get('/\//', forward('/hello/world'));
