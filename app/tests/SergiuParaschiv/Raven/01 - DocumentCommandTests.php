<?php

use SergiuParaschiv\Raven\Models\Foo;
use SergiuParaschiv\Raven\Models\Baz;

use SergiuParaschiv\Raven\Util\Curl\Curl;
use SergiuParaschiv\Raven\DocumentCommand\Load;
use SergiuParaschiv\Raven\DocumentCommand\Store;
use SergiuParaschiv\Raven\DocumentCommand\Delete;


class DocumentCommandTests extends PHPUnit_Framework_TestCase
{
    private $curl;
    
    public function setUp()
    {
        $config = App::di()->get('config');
        
        $this->curl = new Curl($config->get('raven.url') . 'databases/test1/', true);
    }
    
    public function testCreateDocumentWithNoID()
    {
        $foo = new Foo();
        $foo->Bar = 'Bar';
        
        $command = new Store($this->curl);
        $command->execute($foo);
   
        $this->assertNotNull($foo->Id);
    }
    
    public function testCreateDocumentWithID()
    {
        $foo1 = new Foo();
        $foo1->Id = 'tests/10';
        $foo1->Bar = 'Baz';
        
        $command1 = new Store($this->curl);
        $command1->execute($foo1);
        
        $command2 = new Load($this->curl);
        $foo2 = $command2->execute('tests/10', 'SergiuParaschiv\Raven\Models\Foo');
        
        $this->assertEquals('tests/10', $foo2->Id);
        $this->assertEquals('Baz', $foo2->Bar);
    }
    
    public function testDeleteDocument()
    {
        $command1 = new Delete($this->curl);
        $command1->execute('tests/10');

        $command2 = new Load($this->curl);
        $foo = $command2->execute('tests/10', 'SergiuParaschiv\Raven\Models\Foo');
   
        $this->assertNull($foo);
    }
    
    public function testLoadInclude()
    {
        $foo = new Foo();
        $foo->Id = 'foos/special1';
        $foo->Bar = 'BarSpecial';
        
        $command1 = new Store($this->curl);
        $command1->execute($foo);
        
        $baz1 = new Baz();
        $baz1->Id = 'bazs/special1';
        $baz1->Foo = 'foos/special1';
        $baz1->Bla = 'Ana are mere.';
        
        $command1 = new Store($this->curl);
        $command1->execute($baz1);
        
        $command3 = new Load($this->curl);
        $baz2 = $command3->execute('bazs/special1', 
            'SergiuParaschiv\Raven\Models\Baz',
            [
                'Foo' => 'SergiuParaschiv\Raven\Models\Foo'
            ]
        );
        
        $this->assertEquals('BarSpecial', $baz2->Foo->Bar);
    }
    
    public function tearDown()
    {
        $this->curl->close();
    }
}