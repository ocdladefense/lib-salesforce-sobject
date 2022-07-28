<?php
namespace Salesforce;

use File\File as File;

class ContentDocument extends SalesforceFile { // implements ISObject

    public $SObjectName = "ContentVersion";

    protected $Id;

    private $ContentDocumentId;

    private $LinkedEntityId;

    private $id;

    private $title;

    private $fileSize;

    private $extension;

    private $fileType;

    private $ownerName;

    private $ownerId;

    private $linkedEntityId;

    public $isLocal = false;










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



    public function title() {

        return $this->title;
    }

    public function fileSize() {

        return $this->fileSize;
    }

    public function fileType() {

        return $this->fileType;
    }

    public function extension() {

        return $this->extension;
    }

    public function uploadedBy() {

        return empty($this->uploadedBy) ? "OCDLA APP" : $this->uploadedBy;
    }

    public function ownerId() {

        return $this->ownerId;
    }

    public function linkedEntityId() {

        return $this->linkedEntityId;
    }

    public function userIsOwner() {

        return current_user()->getContactId() == $this->ownerId;
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



    // Previously a static method.  We've converted it to be an instance method.
    public function setDocumentData($data) {

            $data = $result["ContentDocument"];
            $doc->id = $result["ContentDocumentId"];
            $doc->title = $data["Title"];
            $doc->fileSize = calculateFileSize($data["ContentSize"]);
            $doc->fileType = $data["FileType"];
            $doc->extension = $data["FileExtension"];
            $doc->uploadedBy = $result["ownerName"];
            $doc->ownerId = $result["ownerId"];
            $doc->linkedEntityId = $result["LinkedEntityId"];

    }




	// Return an associative array of contacts, keyed by the ContentDocumentIds.
	// I don't know why we *need to query for anything here.
	public function getOwners() {

        // We add $links using the setLinks() method from the caller.
        $documentLinks = $this->links;

		$ids = $documentLinks->getField("LinkedEntityId");

		$format = "SELECT Id, Name FROM Contact WHERE Id in (:array)";
		$query = DbHelper::parseArray($format, $ids);
		$resp = loadApi()->query($query);
		
		if(!$resp->success()) throw new Exception($resp->getErrorMessage());

		$contacts = $resp->getQueryResult()->key("Id");

		// We only want the 
		$contactEntities = array_filter($documentLinks->getRecords(), function($link){

			return self::getSobjectType($link["LinkedEntityId"]) == "Contact";
		});

		$owners = [];

		foreach($contactEntities as $entity) {

			$owners[$entity["ContentDocumentId"]] = $contacts[$entity["LinkedEntityId"]];
		
		}

		return $owners;
	}



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
