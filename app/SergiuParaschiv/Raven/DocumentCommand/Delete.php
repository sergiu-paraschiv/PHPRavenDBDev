<?php

namespace SergiuParaschiv\Raven\DocumentCommand;

class Delete {
    private $curl;
    
    public function __construct($curl)
    {
        $this->curl = $curl;
    }
    
    public function execute($id)
    {
        $query = $this->curl->make('docs/' . $id);
        $query->delete()
                ->execute();
    }
}