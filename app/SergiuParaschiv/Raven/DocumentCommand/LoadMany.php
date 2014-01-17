<?php

namespace SergiuParaschiv\Raven\DocumentCommand;

use SergiuParaschiv\Raven\Util\Json;
use SergiuParaschiv\Raven\Util\QueryString;

class LoadMany {
    private $curl;
    
    public function __construct($curl)
    {
        $this->curl = $curl;
    }
    
    public function execute($ids, $className, $includes)
    {
        $queryString = new QueryString('queries');
        
        if(is_array($includes))
        {
            $queryString->parameter('include', array_keys($includes));
        }
        
        $query = $this->curl->make($queryString);
        
        $data = [];
        foreach($ids as $id)
        {
            $data[] = "'" . $id . "'";
        }
        
        $data = '[' . implode(',', $data) . ']';
        
        $response = $query->post($data)
                            ->execute();
                            
        $response = json_decode($response);
        
        $includeResults = [];
        foreach($response->Includes as $includeResult)
        {
            $includeResults[$includeResult->{'@metadata'}->{'@id'}] = $includeResult;
        }
        
        $results = [];
        foreach($response->Results as $result)
        {
            $result = $this->mapResult($result, $className);
            if(is_null($result))
            {
                continue;
            }
            
            foreach($includes as $key => $includeClassName)
            {
                if(
                    array_key_exists($key, $result) 
                    && array_key_exists($result->{$key}, $includeResults)
                ) {
                    $result->{$key} = $this->mapResult($includeResults[$result->{$key}], $includeClassName);
                }
            }
            
            $results[$result->Id] = $result;
        }
        
        return $results;
    }
    
    private function mapResult($result, $className)
    {
        $result = Json::decode($result, $className);
            
        $metadata = $result->{'@metadata'};
        unset($result->{'@metadata'});

        $result = Json::decode($result, $className);

        if(array_key_exists('@id', $metadata))
        {
            $result->Id = $metadata->{'@id'};
            
            return $result;
        }
        
        return null;
    }
}