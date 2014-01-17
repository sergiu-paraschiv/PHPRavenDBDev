<?php

use SergiuParaschiv\Raven\Models\Foo;
use SergiuParaschiv\Raven\Document\DocumentSession;


class DocumentSessionCommandsTests extends PHPUnit_Framework_TestCase
{
    private $session;
    
    public function setUp()
    {
        $config = App::di()->get('config');

        $this->session = new DocumentSession($config->get('raven.url') . 'databases/test1/');
    }
    
    public function testCreateDocument()
    {
        $foo = new Foo();
        $foo->Bar = 'Bar';
        
        $this->session->documentCommands()->store($foo);
   
        $this->assertNotNull($foo->Id);
    }
    
    public function testLoadById()
    {
        $foo = $this->session->documentCommands()->load('Foos/32', 'SergiuParaschiv\Raven\Models\Foo');
        
        $this->assertEquals('Bar2', $foo->Bar);
    }

    public function testHiLoKeyGenerator()
    {
        $lastKey = null;
        for($i = 1; $i < 100; $i++)
        {
            $foo = new Foo();
            $foo->Bar = 'Bar_' . $i;
            $this->session->documentCommands()->store($foo);
            
            $key = intval(str_replace('Foos/', '', $foo->Id));
            if(is_null($lastKey))
            {
                $lastKey = $key;
            }
            else {
                if($key - $lastKey !== 1)
                {
                    $this->fail('Non-consecutive key generated.');
                }
            }
            
            $lastKey = $key;
        }
    }
    
    public function tearDown()
    {
        $this->session->close();
    }
}