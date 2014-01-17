<?php

namespace SergiuParaschiv\Raven;

class RavenJObject {
    
    private $json;
    
    public function __construct($object)
    {
        $this->json = json_encode($object, JSON_UNESCAPED_SLASHES);
    }
    
    public function __toString()
    {
        return $this->json;
    }
}