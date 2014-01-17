<?php

namespace SergiuParaschiv\Raven\Document;

use \InvalidArgumentException;

use SergiuParaschiv\Raven\Util\Curl\Curl;
use SergiuParaschiv\Raven\Util\Queue;
use SergiuParaschiv\Raven\DocumentCommand\Batch;
use SergiuParaschiv\Raven\DocumentCommand\Query;

class DocumentSession {
    private $curl;
    private $idGenerator;
    private $commands;
    private $queue;
    
    public function __construct($url)
    {
        $this->curl = new Curl($url, true);
        $this->idGenerator = new HiLoKeyGenerator($this->curl);
        $this->commands = new DocumentSessionCommands($this->curl, $this->idGenerator);
        $this->queue = new Queue();
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
    
    public function load($id, $className = null)
    {
        if(is_object($id))
        {
            return $this->commands->load($id->Id, get_class($id));
        }
        
        if(is_null($className))
        {
            throw new InvalidArgumentException('You must specify a class name.');
        }
        
        return $this->commands->load($id, $className);
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