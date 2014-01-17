<?php

namespace SergiuParaschiv\Raven\Util;

use \ReflectionClass;

class EntityName {

    public static function asIs($entity)
    {
        $reflector = new ReflectionClass($entity);

        return Inflector::classify($reflector->getShortName());
    }
    
    public static function plural($entity)
    {
        $reflector = new ReflectionClass($entity);

        return Inflector::pluralize(Inflector::classify($reflector->getShortName()));
    }
    
    public static function singular($entity)
    {
        $reflector = new ReflectionClass($entity);

        return Inflector::singularize(Inflector::classify($reflector->getShortName()));
    }
}