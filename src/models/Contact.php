<?php

class Contact extends \Salesforce\SObject {


    /*
    public $FirstName;
    public $LastName;
    public $MailingCity;
    public $MailingState;
    public $Phone;
    public $Email;
    
    public $Ocdla_Expert_Witness_Other_Areas__c;
    public $Ocdla_Occupation_Field_Type__c;
    public $Ocdla_Organization__c;
    public $Ocdla_Expert_Witness_Primary__c;
    */

    public $AreasOfInterest__r;


    public function __construct($id = null){ // Maybe the default constructor takes the Id.

        $this->Id = $id;
    }



    public static function fromSObjects($records){

        $contacts = array();

        foreach($records as $r){

            $c = new Contact($r["Id"]);
            

            foreach(array_keys($r) as $key) {
                $c->{$key} = $r[$key];
            }

            $c->AreasOfInterest__r = $r["AreasOfInterest__r"]["records"];
            
            $contacts[] = $c;
        }

        // var_dump($contacts);exit;

        return $contacts;
    }






    
    public function getAreasOfInterest($asArray = false){

        if(empty($this->AreasOfInterest__r["totalSize"] < 1)) {
            return "";
        }


        $interests = array();

        foreach($this->AreasOfInterest__r as $record) {
            $interests[] = $record["Interest__c"];
        }
        
        return $asArray ? $interests : implode(", ", $interests);
    }



    public function hasInterests() {
        return !empty($this->AreasOfInterest__r);
    }


    
    public function getExpertWitnessOtherAreas(){

        return $this->Ocdla_Expert_Witness_Other_Areas__c;
    }




    public function getPhoneNumericOnly(){

        return preg_replace('/[^a-z\d]/i', '', $this->Phone);
    }




    public function getPrimaryFields($asArray = false){

        $primaryFields = explode(";", $this->Ocdla_Expert_Witness_Primary__c);

        return $asArray ? $primaryFields : implode(", ", $primaryFields);
    }
}