<?php

namespace Salesforce;



class SObject {


    // Representation of the underlying SObject.
    private $sobject;

    
    private $name;

    private $meta;

    private $api;

    protected $Id;



    
    public function getSObject($name) {

        return $this->sobject[$name];
    }

    public function getId() {
        return $this->Id;
    }


    public static function fromSObjects($records){

        $contacts = array();

        foreach($records as $r){

            $c = new self($r["Id"]);

            // $c->AreasOfInterest__r = $r["AreasOfInterest__r"]["records"];

            foreach(array_keys($r) as $key) {
                $c->{$key} = $r[$key];
            }
            
            $contacts[] = $c;
        }

        return $contacts;
    }


    public static function toList($records) {

        $list = array();

        foreach($records as $record) {
            $list []= new SObject($record);
        }

        return $list;
    }

    public function __construct($name){

        $this->name = $name;
    }


    public static function fromMetadata($metadata){

        $sobject = new self($metadata["name"]);
        $sobject->meta = $metadata;

        return $sobject;
    }


    public function getField($fieldName){

        $fields = $this->meta["fields"];

        foreach($fields as $field){

            if($field["name"] == $fieldName){

                return $field;
            }
        }

        return null;
    }


    public function getPicklist($fieldName){

        $fieldMeta = $this->getField($fieldName);

        $pValues = array();

        $pickListValues = $fieldMeta["picklistValues"];

        foreach($pickListValues as $value){

            $pValues[$value["value"]] = $value["label"];
        }

        return $pValues;
    }
}