<?php

namespace SergiuParaschiv\Raven\Util;

use \InvalidArgumentException;

class Json {
    public static function decode($object, $className)
    {
        if (!class_exists($className))
        {
            throw new InvalidArgumentException('Class "' . $className . '" does not exist.');
        }
        
        if(!is_object($object))
        {
            $object = json_decode($object, true);
        }

        $new = new $className();
        
        foreach ($object as $property => &$value)
        {
            $new->$property = &$value;
            unset($object->$property);
        }
        
        unset($value);
        $object = (unset) $object;
        
        return $new;
    }
}