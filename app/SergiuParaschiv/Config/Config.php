<?php

namespace SergiuParaschiv\Config;

class Config {
    private $cache;
    
    public function __construct()
    {
        $this->cache = [];
    }
    
    public function get($name)
    {
        $name = $this->parseName($name);
        
        $this->loadFile($name['file']);
        
        return $this->getKey($name['file'], $name['key']);
    }
    
    private function getKey($file, $key)
    {
        return $this->cache[$file][$key];
    }
    
    private function loadFile($name)
    {
        $this->cache[$name] = include PATH_CONFIG . '/' . $name . '.php';
    }
    
    private function parseName($name)
    {
        $parts = explode('.', $name);
        
        return [
            'file' => $parts[0],
            'key' => $parts[1]
        ];
    }
}