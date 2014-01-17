<?php

use SergiuParaschiv\Raven\Models\Foo;

use SergiuParaschiv\Raven\Document\DocumentSession;


class DocumentSessionTests extends PHPUnit_Framework_TestCase
{
    private $dbURL;
    
    public function setUp()
    {
        $config = App::di()->get('config');
        $this->dbURL = $config->get('raven.url') . 'databases/test1/';
    }
    
    public function testCreateAndModifyDocument()
    {
        $session1 = new DocumentSession($this->dbURL);
        
        $foo1 = new Foo();
        $foo1->Bar = 'Bar1';
        $session1->store($foo1);

        $foo2 = $session1->load($foo1);
        $this->assertNull($foo2);
        
        $foo1->Bar = 'Bar2';
        $session1->store($foo1);
        
        $session1->saveChanges();
        
        $session2 = new DocumentSession($this->dbURL);
        
        $foo3 = $session2->load($foo1);
        $this->assertEquals('Bar2', $foo3->Bar);
        
        $session2->saveChanges();
    }
    
    public function testCreateAndDeleteDocument()
    {
        $session1 = new DocumentSession($this->dbURL);
        
        $foo1 = new Foo();
        $foo1->Bar = 'Bar1';
        $session1->store($foo1);
        $session1->delete($foo1);
        
        $session1->saveChanges();
        
        $session2 = new DocumentSession($this->dbURL);
        
        $foo3 = $session2->load($foo1);
        $this->assertNull($foo3);
        
        $session2->saveChanges();
    }
    
    public function testLoadByEntity()
    {
        $session = new DocumentSession($this->dbURL);
        
        $foo = new Foo();
        $foo->Id = 'Foos/32';
        
        $foo = $session->load($foo);
        
        $this->assertEquals('Bar2', $foo->Bar);
    }
}