<?php

namespace SergiuParaschiv\DependencyInjector;

use \ReflectionClass;

class DI {
    const INSTANCE = 'instance';
    const SINGLETON = 'singleton';
    
    private static $registry;
    
    public static function bind($alias, $class)
    {
        self::$registry[$alias] = [
            'class' => $class,
            'type' => self::INSTANCE
        ];
    }
    
    public static function singleton($alias, $class)
    {
        self::$registry[$alias] = [
            'class' => $class,
            'type' => self::SINGLETON,
            'instance' => null
        ];
    }
    
    public static function make($alias, $args = [])
    {
        if(!array_key_exists($alias, self::$registry))
        {
            throw new DIException('Unknown class alias "' . $alias . '".');
        }
        
        if(self::$registry[$alias]['type'] === self::INSTANCE)
        {            
            return self::getClassInstance(self::$registry[$alias]['class'], $args);
        }
        else  if(self::$registry[$alias]['type'] === self::SINGLETON)
        {
            throw new DIException('"' . self::$registry[$alias]['type'] . '" is a singleton and cannot be instantiated.');
        }
        else
        {
            throw new DIException('Unknown instance type "' . self::$registry[$alias]['type'] . '".');
        }
    }
    
    public static function get($alias)
    {
        if(self::$registry[$alias]['type'] === self::SINGLETON)
        {
            if(is_null(self::$registry[$alias]['instance']))
            {
                self::$registry[$alias]['instance'] = self::getClassInstance(self::$registry[$alias]['class'], []);
            }
            
            return self::$registry[$alias]['instance'];
        }
        else if(self::$registry[$alias]['type'] === self::INSTANCE)
        {            
            throw new DIException('"' . self::$registry[$alias]['type'] . '" is not a singleton.');
        }
        else
        {
            throw new DIException('Unknown instance type "' . self::$registry[$alias]['type'] . '".');
        }
    }
    
    private static function getClassInstance($class, $args)
    {
        $reflector = new ReflectionClass($class);
        
        return $reflector->newInstanceArgs($args);
    }
}