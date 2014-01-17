<?php

namespace SergiuParaschiv\Raven\Document;

use SergiuParaschiv\Raven\DocumentCommand\Load;
use SergiuParaschiv\Raven\DocumentCommand\LoadMany;
use SergiuParaschiv\Raven\DocumentCommand\Store;
use SergiuParaschiv\Raven\DocumentCommand\Delete;

class DocumentSessionCommands {
    
    private $curl;
    private $idGenerator;
    
    public function __construct($curl, $idGenerator)
    {
        $this->curl = $curl;
        $this->idGenerator = $idGenerator;
    }
    
    public function load($id, $className, $includes = [])
    {
        $command = new Load($this->curl);
        
        return $command->execute($id, $className, $includes);
    }
    
    public function loadMany($ids, $className, $includes)
    {
        $command = new LoadMany($this->curl);
        
        return $command->execute($ids, $className, $includes);
    }

    public function store($entity)
    {
        if(is_null($entity->Id))
        {
            $entity->Id = $this->idGenerator->nextId($entity);
        }
        
        $command = new Store($this->curl);
        
        return $command->execute($entity);
    }
    
    public function delete($id)
    {
        if(is_object($id))
        {
            $id = $id->Id;
        }
        
        $command = new Delete($this->curl);
        $command->execute($id);
    }
}