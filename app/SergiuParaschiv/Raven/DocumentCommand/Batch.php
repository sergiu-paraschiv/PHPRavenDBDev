<?php

namespace SergiuParaschiv\Raven\DocumentCommand;

use SergiuParaschiv\Raven\Util\Queue;

class Batch {
    private $curl;
    
    public function __construct($curl)
    {
        $this->curl = $curl;
    }
    
    public function execute(Queue $queue)
    {
        $batch = [];
        while(true)
        {
            $item = $queue->shift();
            if(is_null($item))
            {
                break;
            }
            
            $batch[] = $item->toJson();
        }
        
        $batch = '[' . implode(',', $batch) . ']';
        
        $query = $this->curl->make('bulk_docs');
        $query->post($batch)
                ->execute();
    }
}

