<?php

use SergiuParaschiv\Raven\Models\Foo;
use SergiuParaschiv\Raven\Models\Baz;

use SergiuParaschiv\Raven\Document\DocumentSession;

class DocumentSessionLoadTests extends PHPUnit_Framework_TestCase
{
    private $dbURL;
    
    public function setUp()
    {
        $config = App::di()->get('config');
        $this->dbURL = $config->get('raven.url') . 'databases/test1/';
    }
    
    public function testLoadMultiple()
    {
        $session = new DocumentSession($this->dbURL);

        $foos = $session->load(['Foos/qqq', 'Foos/67', 'Foos/68', 'Foos/69'], 'SergiuParaschiv\Raven\Models\Foo');
        
        $this->assertEquals(3, count($foos));
        $this->assertEquals('Foos/67', $foos['Foos/67']->Id);
    }
    
    public function testLoadMultipleIncluding()
    {
        $session = new DocumentSession($this->dbURL);

        $bazs = $session
                ->including([
                    'Foo' => 'SergiuParaschiv\Raven\Models\Foo'
                ])
                ->load(['bazs/special1'], 'SergiuParaschiv\Raven\Models\Baz');

        $this->assertEquals('BarSpecial', $bazs['bazs/special1']->Foo->Bar);
    }
    
    public function testLoadNonExistingInclude()
    {
        $session = new DocumentSession($this->dbURL);

        $foos = $session
                    ->including([
                        'Baz' => 'SergiuParaschiv\Raven\Models\Baz'
                    ])
                    ->load(['Foos/qqq', 'Foos/67', 'Foos/68', 'Foos/69'], 'SergiuParaschiv\Raven\Models\Foo');
                    
        $this->assertFalse(array_key_exists('Baz', $foos['Foos/67']));
    }
}