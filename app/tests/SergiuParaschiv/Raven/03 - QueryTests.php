<?php

use SergiuParaschiv\Raven\Models\Foo;
use SergiuParaschiv\Raven\Models\Indexes\FoosByBar;

use SergiuParaschiv\Raven\Util\Curl\Curl;
use SergiuParaschiv\Raven\DocumentCommand\Query;

class QueryTests extends PHPUnit_Framework_TestCase
{
    private $curl;
    
    public function setUp()
    {
        $config = App::di()->get('config');
        
        $this->curl = new Curl($config->get('raven.url') . 'databases/test1/', true);
    }
    
    public function testSimpleQuery()
    {
        $index = new FoosByBar();
        $index->Bar = 'Bar';
        
        $query = new Query($this->curl);
        $query->on($index);
        
        $foos = $query->getAs('SergiuParaschiv\Raven\Models\Foo');
                                
        $this->assertNotNull($foos[0]);
    }
    
    public function tearDown()
    {
        $this->curl->close();
    }
}