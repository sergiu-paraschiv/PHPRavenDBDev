<?php

namespace SergiuParaschiv\Raven\Document;

use \StdClass;

use SergiuParaschiv\Raven\Util\EntityName;
use SergiuParaschiv\Raven\Abstractions\Data\HiLoDocument;
use SergiuParaschiv\Raven\DocumentCommand\Load;
use SergiuParaschiv\Raven\DocumentCommand\Store;

class HiLoKeyGenerator {
    const CAPACITY = 32;
    const DOC_PREFIX = 'RavenPHP/HiLo/';
    
    private $curl;
    private static $ranges;
    
    public function __construct($curl)
    {
        $this->curl = $curl;
        
        if(is_null(self::$ranges))
        {
            self::$ranges = [];
        }
    }
    
    public function nextId($entity)
    {
        $name = $this->rangeName($entity);
        $docId = self::DOC_PREFIX . $name;
   
        $doc = $this->ensureDocExists($docId);
        $range = $this->ensureRangeExists($name, $doc);
        
        if($range->Min >= $range->Max)
        {
            $range->Max = $this->getNextRange($range, $doc);
        }
        
        $nextId = $name . '/' . $range->Min;
        
        $range->Min += 1;
        
        return $nextId;
    }
    
    private function getNextRange($range, $doc)
    {
        $nextRange = $range->Amplitude * self::CAPACITY;
        
        $doc->Max += $nextRange;
        $command = new Store($this->curl);
        $command->execute($doc);
        
        $range->Max = $doc->Max;
        $range->Amplitude += 1;
        
        return $range->Max;
    }
    
    private function ensureDocExists($docId)
    {
        $command = new Load($this->curl);
        $doc = $command->execute($docId, 'SergiuParaschiv\Raven\Abstractions\Data\HiLoDocument');
        
        if(is_null($doc))
        {
            $doc = new HiLoDocument();
            $doc->Id = $docId;
            $doc->Max = self::CAPACITY;
            
            $command = new Store($this->curl);
            $command->execute($doc);
        }
        
        return $doc;
    }
    
    private function ensureRangeExists($name, $doc)
    {
        if(!array_key_exists($name, self::$ranges))
        {
            self::$ranges[$name] = new StdClass();
            self::$ranges[$name]->Min = $doc->Max;
            self::$ranges[$name]->Max = $doc->Max;
            self::$ranges[$name]->Amplitude = 1;
        }
        
        return self::$ranges[$name];
    }
    
    private function rangeName($entity)
    {
        return EntityName::plural($entity);
    }
}