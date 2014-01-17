<?php

namespace SergiuParaschiv\Raven\DocumentCommand;

use SergiuParaschiv\Raven\Util\EntityName;
use SergiuParaschiv\Raven\Util\Json;

class Query {

    private $curl;
    private $index;
    
    public function __construct($curl)
    {
        $this->curl = $curl;
    }
    
    public function on($index)
    {
        $this->index = $index;
        
        return $this;
    }
    
    public function getAs($className)
    {
        $query = $this->curl->make('indexes/' . EntityName::singular($this->index) . '?query=' . $this->makeQuery());

        $response = $query->get()
                            ->execute();
                            
        $response = json_decode($response);
        
        $results = [];
        foreach($response->Results as $result)
        {
            $metadata = $result->{'@metadata'};
            unset($result->{'@metadata'});
            
            $result = Json::decode($result, $className);
            $result->Id = $metadata->{'@id'};
            
            $results[] = $result;
        }
        
        return $results;
    }
    
    private function makeQuery()
    {
        $query = [];
        
        foreach($this->index as $key => $value)
        {
            $query[] = $key . ':' . $value;
        }
        
        return implode('&', $query);
    }
}