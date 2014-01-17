<?php

namespace SergiuParaschiv\Raven\Document;

use \InvalidArgumentException;

use SergiuParaschiv\Raven\Util\Curl\Curl;
use SergiuParaschiv\Raven\Util\Queue;
use SergiuParaschiv\Raven\DocumentCommand\Batch;
use SergiuParaschiv\Raven\DocumentCommand\Query\Query;

class DocumentSession {
    private $curl;
    private $idGenerator;
    private $commands;
    private $queue;
    private $includes;
    
    public function __construct($url)
    {
        $this->curl = new Curl($url, true);
        $this->idGenerator = new HiLoKeyGenerator($this->curl);
        $this->commands = new DocumentSessionCommands($this->curl, $this->idGenerator);
        $this->queue = new Queue();
        $this->includes = [];
    }
    
    public function store($entity)
    {
        if(is_null($entity->Id))
        {
            $entity->Id = $this->idGenerator->nextId($entity);
        }
        
        $item = new BatchItem(BatchItem::STORE, $entity);
        $this->queue->push($item);
    }
    
    public function delete($id)
    {
        if(is_object($id))
        {
            $id = $id->Id;
        }
        
        $item = new BatchItem(BatchItem::DELETE, $id);
        $this->queue->push($item);
    }
    
    public function including($includes)
    {
        $this->includes = $includes;
        
        return $this;
    }
    
    public function load($id, $className = null)
    {
        if(is_array($id))
        {
            return $this->commands->loadMany($id, $className, $this->includes);
        }
        
        if(is_object($id))
        {
            return $this->commands->load($id->Id, get_class($id), $this->includes);
        }
        
        if(is_null($className))
        {
            throw new InvalidArgumentException('You must specify a class name.');
        }
        
        return $this->commands->load($id, $className, $this->includes);
    }
    
    public function query($index)
    {
        $query = new Query($this->curl);
        $query->on($index);
        
        return $query;
    }
    
    public function documentCommands()
    {
        return $this->commands;
    }
    
    public function saveChanges()
    {
        $batch = new Batch($this->curl);
        $batch->execute($this->queue);
    }
    
    public function close()
    {
        $this->curl->close();
    }
}