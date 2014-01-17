<?php

namespace SergiuParaschiv\PHPRavenDB\Abstractions\Data;

use \StdClass;

class DatabaseDocument {
    public $id = '';
    public $settings;
    
    public function __construct($id)
    {
        $this->id = $id;
        
        $this->settings = new StdClass();
        $this->settings->{'Raven/DataDir'} = '~/' . $id;
    }
}