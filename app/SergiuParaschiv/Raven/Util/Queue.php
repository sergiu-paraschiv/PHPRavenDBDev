<?php

namespace SergiuParaschiv\Raven\Util;

class Queue {
    private $queue;
    
    public function __construct()
    {
        $this->queue = [];
    }
    
    public function push($item)
    {
        $this->queue[] = $item;
    }
    
    public function shift()
    {
        return array_shift($this->queue);
    }
}