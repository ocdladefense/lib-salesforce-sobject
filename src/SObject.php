<?php

namespace Salesforce;


class SObject {



    private $sobject;



    public function __construct($record) {
        $this->sobject = $record;
    }



    public function getSObject($name) {

        return $this->sobject[$name];
    }

    public static function toList($records) {

        $list = array();

        foreach($records as $record) {
            $list []= new SObject($record);
        }

        return $list;
    }

}