<?php

namespace SergiuParaschiv\Raven\Document;

class DocumentStore {
    
    private $url;
    
    public function __construct($url)
    {        
        $this->url = $url;
    }
    
    public function openSession($databaseName = '')
    {
        return new DocumentSession($this->url . $databaseName);
    }
}