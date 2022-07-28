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



    public function id() {

        return $this->id;
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

    public static function fromContentDocumentLinkQueryResult($contentDocumentLinkQueryResults) {

        $documents = [];

        foreach($contentDocumentLinkQueryResults as $result) {

            $data = $result["ContentDocument"];

            $doc = new self();
            $doc->id = $result["ContentDocumentId"];
            $doc->title = $data["Title"];
            $doc->fileSize = calculateFileSize($data["ContentSize"]);
            $doc->fileType = $data["FileType"];
            $doc->extension = $data["FileExtension"];
            $doc->uploadedBy = $result["ownerName"];
            $doc->ownerId = $result["ownerId"];
            $doc->linkedEntityId = $result["LinkedEntityId"];
            

            $documents[] = $doc;
        }

        return $documents;
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
