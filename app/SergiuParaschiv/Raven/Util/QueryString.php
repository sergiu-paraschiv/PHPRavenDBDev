<?php

namespace SergiuParaschiv\Raven\Util;

class QueryString {
    
    private $base;
    private $parameters;
    
    public function __construct($base, $parameters = [])
    {
        $this->base = $base;
        $this->parameters = $parameters;
    }
    
    public function parameter($name, $value)
    {
        $this->parameters[$name] = $value;
        
        return $this;
    }
    
    public function removeParameter($name)
    {
        if(array_key_exists($name, $this->parameters))
        {
            unset($this->parameters[$name]);
        }
        
        return $this;
    }
    
    public function __toString()
    {
        $out = $this->base;
        
        if(count($this->parameters) > 0)
        {
            $parameters = [];
            foreach($this->parameters as $name => $value)
            {
                if(is_array($value))
                {
                    foreach($value as $aValue)
                    {
                        $parameters[] = $name . '=' . $aValue;
                    }
                }
                else {
                    $parameters[] = $name . '=' . $value;
                }
            }
            
            $out .= '?' . implode('&', $parameters);
        }
        
        return $out;
    }
}