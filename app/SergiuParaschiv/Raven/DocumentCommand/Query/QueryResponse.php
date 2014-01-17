<?php

namespace SergiuParaschiv\Raven\DocumentCommand\Query;

use SergiuParaschiv\Raven\Util\Json;

class QueryResponse {

    private $items;
    private $total;
    private $stale;
    
    public function __construct($response, $className)
    {
        $this->items = [];
        $this->stale = $response->IsStale;
        $this->total = $response->TotalResults;
        $this->skipped = $response->SkippedResults;
        
        foreach($response->Results as $json)
        {
            
            $metadata = $json->{'@metadata'};
            unset($json->{'@metadata'});

            $result = Json::decode($json, $className);
  
            if(array_key_exists('@id', $metadata))
            {
                $result->Id = $metadata->{'@id'};
            }
            else if(array_key_exists('__document_id', $json))
            {
                $result->Id = $json->{'__document_id'};
            }
            
            $this->items[] = $result;
        }
    }
    
    public function get($index)
    {
        if(!array_key_exists($index, $this->items))
        {
            return null;
        }
        
        return $this->items[$index];
    }
    
    public function size()
    {
        return count($this->items);
    }
    
    public function totalResults()
    {
        return $this->total;
    }
    
    public function skippedResults()
    {
        return $this->skipped;
    }
    
    public function isStale()
    {
        return $this->stale;
    }
    
    public function asArray()
    {
        return $this->items;
    }
}