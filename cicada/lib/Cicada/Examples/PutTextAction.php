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
namespace Cicada\Examples;


use Cicada\Action;
use Cicada\Responses\EchoResponse;

class PutTextAction extends Action {

    private $putData;

    /**
     * Lazy actions are created by reflection and thus need an constructor
     * which can be called without params
     */
    function __construct() {
    }

    public function execute() {
        $this->putData = $this->readAllData();
        return self::SUCCESS;
    }

    public function getResponse() {
        return new EchoResponse('Read put data: ' . $this->putData);
    }
}