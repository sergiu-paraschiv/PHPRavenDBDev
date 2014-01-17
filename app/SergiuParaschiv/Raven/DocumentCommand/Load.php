<?php

namespace SergiuParaschiv\Raven\DocumentCommand;

use SergiuParaschiv\Raven\Util\Json;

class Load {
    private $curl;
    
    public function __construct($curl)
    {
        $this->curl = $curl;
    }
    
    public function execute($id, $className, $includes = [])
    {
        $query = $this->curl->make('docs/' . $id);
        $response = $query->get()
                            ->execute();
                
        if(is_null($response))
        {
            return null;
        }
        
        $response = Json::decode($response, $className);
        
        $response->Id = $id;
        
        $response = $this->processIncludes($response, $includes);
        
        return $response;
    }
    
    private function processIncludes($entity, $includes)
    {
        foreach($includes as $include)
        {
            $entity->{$include['key']} = $this->execute($entity->{$include['key']}, $include['className'], $include['includes']);
        }
        
        return $entity;
    }
}