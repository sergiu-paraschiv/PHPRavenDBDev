<?php

use SergiuParaschiv\Raven\Models\Foo;
use SergiuParaschiv\Raven\Models\Baz;
use SergiuParaschiv\Raven\Models\Indexes\FoosByBar;
use SergiuParaschiv\Raven\Models\Indexes\Bazs;

use SergiuParaschiv\Raven\Util\Curl\Curl;
use SergiuParaschiv\Raven\DocumentCommand\Query\Query;

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
        
        $foos = $query->on($index)
                        ->type('SergiuParaschiv\Raven\Models\Foo')
                        ->get();
                                
        $this->assertNotNull($foos->get(0));
    }
    
    public function testPagedQuery()
    {
        $index = new FoosByBar();
        $index->Bar = 'Bar';
        
        $query = new Query($this->curl);
        
        $foos = $query->on($index)
                        ->type('SergiuParaschiv\Raven\Models\Foo')
                        ->take(10)
                        ->skip(10)
                        ->get();
                                
        $this->assertEquals(10, $foos->size());
    }
    
    public function testProjection()
    {
        $index = new Bazs();
        
        $query = new Query($this->curl);
        
        $bazs = $query->on($index)
                        ->type('SergiuParaschiv\Raven\Models\Baz')
                        ->select('Foo')
                        ->get();
        
        $this->assertNotNull($bazs->get(0));
        $this->assertNull($bazs->get(0)->Bla);
    }
    
    public function testMultipleProjection()
    {
        $index = new Bazs();
        
        $query = new Query($this->curl);
        
        $bazs = $query->on($index)
                        ->type('SergiuParaschiv\Raven\Models\Baz')
                        ->select(['Foo', 'Bla'])
                        ->get();
        
        $this->assertNotNull($bazs->get(0));
        $this->assertNotNull($bazs->get(0)->Bla);
    }
    
    public function testSorting()
    {
        $index = new FoosByBar();
        
        $query = new Query($this->curl);
        
        $foos = $query->on($index)
                        ->type('SergiuParaschiv\Raven\Models\Foo')
                        ->orderByDescending('Bar')
                        ->get();
                                
        $this->assertEquals('BarSpecial', $foos->get(0)->Bar);
    }
    
    public function tearDown()
    {
        $this->curl->close();
    }
}