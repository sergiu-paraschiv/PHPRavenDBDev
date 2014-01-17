<?php

namespace SergiuParaschiv\Raven\DocumentCommand\Query;

use SergiuParaschiv\Raven\Util\EntityName;
use SergiuParaschiv\Raven\Util\QueryString;

class Query {

    const DEFAULT_TAKE = 25;
    const DEFAULT_SKIP = 0;

    private $curl;
    private $index;
    private $className;
    private $take;
    private $skip;
    private $projection;
    private $order;
    
    public function __construct($curl)
    {
        $this->curl = $curl;
        $this->className = '\StdClass';
        $this->take = self::DEFAULT_TAKE;
        $this->skip = self::DEFAULT_SKIP;
        $this->projection = [];
        $this->order = '';
    }
    
    public function on($index)
    {
        $this->index = $index;
        
        return $this;
    }
    
    public function type($className)
    {
        $this->className = $className;
        
        return $this;
    }
    
    public function take($take)
    {
        $this->take = $take;
        
        return $this;
    }
    
    public function skip($skip)
    {
        $this->skip = $skip;
        
        return $this;
    }
    
    public function select($fields)
    {
        if(!is_array($fields))
        {
            $fields = [$fields];
        }
        
        $this->projection = $fields;
        
        return $this;
    }
    
    public function orderBy($field)
    {
        $this->order = $field;
        
        return $this;
    }
    
    public function orderByDescending($field)
    {
        $field = ltrim($field, '-');
        
        return $this->orderBy('-' . $field);
    }
    
    public function get()
    {
        $queryString = new QueryString('indexes/' . EntityName::asIs($this->index));
        
        $index = $this->makeQuery();
        if($index !== '')
        {
            $queryString->parameter('query', $index);
        }
        
        if(count($this->projection) > 0)
        {
            $queryString->parameter('fetch', $this->projection);
        }
        
        if($this->order !== '')
        {
            $queryString->parameter('sort', $this->order);
        }
        
        $queryString->parameter('start', $this->skip);
        $queryString->parameter('pageSize', $this->take);

        $query = $this->curl->make($queryString);

        $response = $query->get()->execute();
                            
        $response = json_decode($response);

        return new QueryResponse($response, $this->className);
    }
    
    private function makeQuery()
    {
        $query = [];
        
        foreach($this->index as $key => $value)
        {
            if(!is_null($value))
            {
                $query[] = $key . ':' . $value;
            }
        }
        
        return implode('&', $query);
    }
}