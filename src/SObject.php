<?php

namespace Salesforce;



class SObject {


    // Representation of the underlying SObject.
    private $sobject;

    
    private $name;

    private $meta;

    private $api;



    
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