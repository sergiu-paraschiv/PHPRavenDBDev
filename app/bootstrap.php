<?php

define('PATH_VENDOR', __DIR__ . '/../vendor');
define('PATH_CONFIG', __DIR__ . '/config');

require PATH_VENDOR . '/autoload.php';

use SergiuParaschiv\DependencyInjector\DI as DI;

$DIMap = [
    'config' => [
        'class' => 'SergiuParaschiv\Config\Config',
        'type' => DI::SINGLETON
    ],
    
    'curl' => [
        'class' => 'SergiuParaschiv\Raven\Util\Curl\Curl',
        'type' => DI::INSTANCE
    ],
];

class App {
    private static $DI;
    
    public static function di()
    {
        global $DIMap;
        
        if(is_null(self::$DI))
        {
            self::$DI = new SergiuParaschiv\DependencyInjector\DI;
            
            foreach($DIMap as $alias => $meta)
            {
                if($meta['type'] === DI::INSTANCE)
                {
                    self::$DI->bind($alias, $meta['class']);
                }
                else if($meta['type'] === DI::SINGLETON) { 
                     self::$DI->singleton($alias, $meta['class']);
                }
            }
        }
        
        return self::$DI;
    }
}