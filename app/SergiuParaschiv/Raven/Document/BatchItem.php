<?php

namespace SergiuParaschiv\Raven\Document;

use SergiuParaschiv\Raven\RavenJObject;
use SergiuParaschiv\Raven\Util\EntityName;

class BatchItem {

    const STORE = 'store';
    const DELETE = 'delete';
    
    public $type;
    public $entity;
    
    public function __construct($type, $entity)
    {
        $this->type = $type;
        $this->entity = $entity;
    }
    
    public function toJson()
    {
        if($this->type === self::STORE)
        {
            $copy = clone $this->entity;
            unset($copy->Id);        
            $jCopy = new RavenJObject($copy) . '';
        
            return "{ 
                        Method: 'PUT',
                        Document: " . $jCopy . ",
                        Metadata: { 'Raven-Entity-Name': '" . EntityName::singular($this->entity) . "' },
                        Key: '" . $this->entity->Id . "'
                    }";
        }
        else if($this->type === self::DELETE)
        {
            return "{ 
                        Method: 'DELETE',
                        Key: '" . $this->entity . "'
                    }";
        }
    }
}