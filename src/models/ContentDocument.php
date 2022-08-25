<?php
namespace Salesforce;

use File\File as File;

class ContentDocument extends SalesforceFile { // implements ISObject

    public $SObjectName = "ContentVersion";

    protected $Id;

    private $ContentDocumentId;

    private $LinkedEntityId;

    public $data;

    public $linkedEntities = [];

    private $linkedEntityId;

    public $isLocal = false;

    public $sharedWith = [];

    public $uploadedBy;




    public function __construct($id = null){ // Maybe the default constructor takes the Id.

        $this->Id = $id;
    }

    public function setId($id){

        $this->Id = $id;
    }

    public function setLinkedEntityId($id){

        $this->LinkedEntityId = $id;
    }

    public function setContentDocumentId($id){

        $this->ContentDocumentId = $id;
    }

    public function getId(){

        return $this->Id;
    }

    public function getLinkedEntityId(){

        return $this->LinkedEntityId;
    }

    public function getContentDocumentId(){

        return $this->ContentDocumentId;
    }

    public function setDocumentData($data) {

        $this->data = $data;
    }

    public function setLinkedEntities($links) {

        $this->linkedEntities = array_map(function($link){

            return $link["LinkedEntityId"];

        }, $links);
    }

    public function getLinkedEntities() {

        return $this->linkedEntities;
    }

    public function id() {

        return $this->Id;
    }

    public function title() {

        return $this->data["Title"];
    }

    public function fileSize() {

        return calculateFileSize($this->data["ContentSize"]);
    }

    public function fileType() {

        return $this->data["FileType"];
    }

    public function extension() {

        return $this->data["FileExtension"];
    }


    public function linkedEntityId() {

        return $this->linkedEntityId;
    }


    public function setUploadedBy($name) {

        $this->uploadedBy = $name;
    }

    public function getUploadedBy() {

        return $this->uploadedBy;
    }


    public function getUploadedById() {

        // SObject Prefix
        $contactPrefix = "003";

        $uploadedByIds = array_filter($this->linkedEntities, function($id) use($contactPrefix) {

            return substr($id, 0, 3) == $contactPrefix;
        });

        return array_values($uploadedByIds)[0];
    }


    // public function getSharedWithIds(){

    //     // We don't need to see the admin users or contacts in the "shared with" column.
    //     $ignoredPrefixes = ["Contact" => "003", "User" => "005"];

    //     $sharedWith = array_filter($this->linkedEntities, function($id) use($ignoredPrefixes) {

    //         $idPrefix = substr($id, 0, 3);

    //         return !in_array($idPrefix, $ignoredPrefixes);
    //     });

    //     return $sharedWith;

    // }




    public function getSharedWith() {

        return $this->sharedWith;
    }

    public function addSharedWith($name) {

        $this->sharedWith[] = $name;
    }

    // Is the contact the person who uploaded the document?
    public function isOwner($contactId) {

        return in_array($contactId, $this->linkedEntities);
    }

    
    public static function fromFile(File $file){

        $sfFile = new ContentDocument();
        $sfFile->setPath($file->getPath());
        $sfFile->setName($file->getName());
        $sfFile->isLocal = true;

        return $sfFile;
    }

    public static function fromArray($obj){

        $sfFile = new Attachment();
        $sfFile->Id = $ojb["id"];

        return $sfFile;
    }





	// Return an associative array of contacts, keyed by the ContentDocumentIds.
	// I don't know why we *need to query for anything here.
	// public function getOwners() {

    //     // We add $links using the setLinks() method from the caller.
    //     $documentLinks = $this->links;

	// 	$ids = $documentLinks->getField("LinkedEntityId");

	// 	$format = "SELECT Id, Name FROM Contact WHERE Id in (:array)";
	// 	$query = DbHelper::parseArray($format, $ids);
	// 	$resp = loadApi()->query($query);
		
	// 	if(!$resp->success()) throw new Exception($resp->getErrorMessage());

	// 	$contacts = $resp->getQueryResult()->key("Id");

	// 	// We only want the 
	// 	$contactEntities = array_filter($documentLinks->getRecords(), function($link){

	// 		return self::getSobjectType($link["LinkedEntityId"]) == "Contact";
	// 	});

	// 	$owners = [];

	// 	foreach($contactEntities as $entity) {

	// 		$owners[$entity["ContentDocumentId"]] = $contacts[$entity["LinkedEntityId"]];
		
	// 	}

	// 	return $owners;
	// }



    public static function fromJson($json){

        $obj = json_decode($json);

        $sfFile = new Attachment();
        $sfFile->Id = $ojb->id;

        return $sfFile;
    }

    // Always produce an object that is compatible with the salesforce simple object endpoint.
    public function getSObject(){ 

        return array(
            "Title"              => $this->getName(),
            "ContentDocumentId"  => $this->getContentDocumentId(),
            "PathOnClient"       => $this->getPath()
        );
    }
}
