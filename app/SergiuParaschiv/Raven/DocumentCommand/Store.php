<?php

namespace SergiuParaschiv\Raven\DocumentCommand;

use SergiuParaschiv\Raven\Util\EntityName;
use SergiuParaschiv\Raven\RavenJObject;

class Store {
    private $curl;
    
    public function __construct($curl)
    {
        $this->curl = $curl;
    }
    
    public function execute($entity)
    {
        $copy = clone $entity;        
        unset($copy->Id);        
        $jCopy = new RavenJObject($copy) . '';
        
        if(is_null($entity->Id))
        {
            $query = $this->curl->make('docs');
            
            $response = $query->post($jCopy)
                                ->header('Raven-Entity-Name', EntityName::singular($entity))
                                ->execute();
            
            $response = json_decode($response);
            
            $entity->Id = $response->Key;
            
            return $entity;
        }
        else {
            $query = $this->curl->make('docs/' . $entity->Id);

            $response = $query->put($jCopy)
                                ->header('Raven-Entity-Name', EntityName::singular($entity))
                                ->execute();
                                
            return $entity;
        }
        
        return null;
    }
}