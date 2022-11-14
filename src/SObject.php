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

    public function getId() {
        return $this->Id;
    }


    public static function fromSObjects($records){

        return array_map(function($r) {
            $c = new self($r["Id"]);
            // $c->AreasOfInterest__r = $r["AreasOfInterest__r"]["records"];
            foreach(array_keys($r) as $key) {
                $c->{$key} = $r[$key];
            }

            return $c;
        }, $records);
    }


    /**
     * Examples:
     *  // Get all selected values for this SObject's multipicklist "myPicklist" field.
     *  ->getValues("myPicklist");
     * 
     *  // You can also specify a relationship and field pair.
     *  ->getValues("AreasOfInterest__r.Interest__c");
     * 
     *  // Or specify multiple fields to retrieve in the query.
     *  -->getValues("AreasOfInterest__r.Interest__c","AreasOfInterest__r.Id");
     * 
     * @param query String A string specifying SObject query syntax.  SObject query syntax is a shorthand relationship/object/field identifiers to retrieve scalar or array values from this Salesforce record.
     */
    public function getObject($query) {

        $parts = explode(".", $query);

        $field1 = array_shift($parts);
        
		$start = $this->{$field1};

        if(count($parts) == 0) {
            return $start;
        }

        return array_reduce($parts, function($carry, $current) {
            return $carry[$current];
        }, $start);
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