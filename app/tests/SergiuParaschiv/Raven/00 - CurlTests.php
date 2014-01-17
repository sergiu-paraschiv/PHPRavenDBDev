<?php

class CurlTests extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException SergiuParaschiv\Raven\Util\Curl\CurlException
     */
    public function testQueryException()
    {
        $config = App::di()->get('config');
        $curl = App::di()->make('curl');
        
        $query = $curl->make($config->get('raven.url'));
        $query->execute();
    }
    
    public function testNonReusableSessionQuery()
    {
        $config = App::di()->get('config');
        $curl = App::di()->make('curl');
        
        $query = $curl->make($config->get('raven.url') . 'databases');
        $response = $query->execute();
        
        $this->assertNotNull($response);
    }
    
    public function testReusableSessionQueries()
    {
        $config = App::di()->get('config');
        $curl = App::di()->make('curl', [$config->get('raven.url'), true]);
        
        $query1 = $curl->make('databases');
        $query2 = $curl->make('databases');
        
        $response1 = $query1->execute();
        $response2 = $query2->execute();
        
        $curl->close();
        
        $this->assertNotNull($response1);
        $this->assertNotNull($response2);
    }
    
    public function testBasicGet()
    {
        $config = App::di()->get('config');
        $curl = App::di()->make('curl');
        
        $query = $curl->make($config->get('raven.url') . 'databases');
        $response = $query->get()->execute();
        
        $this->assertNotNull($response);
    }
    
    public function testPut()
    {
        $config = App::di()->get('config');
        $curl = App::di()->make('curl', [$config->get('raven.url') . 'databases/test1/', false]);
        
        $query = $curl->make('docs/tests/1');
        $response = $query
                        ->put('{"Test": 1}')
                        ->execute();
        
        $this->assertNotNull($response);
    }
    
    public function testPutWithHeader()
    {
        $config = App::di()->get('config');
        $curl = App::di()->make('curl', [$config->get('raven.url') . 'databases/test1/', false]);
        
        $query = $curl->make('docs/tests/2');
        $response = $query
                        ->put('{"Test": 2}')
                        ->header('Raven-Entity-Name', 'tests')
                        ->execute();
        
        $this->assertNotNull($response);
    }
    
    public function testPostWithHeader()
    {
        $config = App::di()->get('config');
        $curl = App::di()->make('curl', [$config->get('raven.url') . 'databases/test1/', false]);
        
        $query = $curl->make('docs');
        $response = $query
                        ->post('{"Test": 2}')
                        ->header('Raven-Entity-Name', 'tests')
                        ->execute();

        $this->assertNotNull($response);
    }
    
    public function testDelete()
    {
        $config = App::di()->get('config');
        $curl = App::di()->make('curl', [$config->get('raven.url') . 'databases/test1/', false]);
        
        $query = $curl->make('docs/tests/1');
        $query
            ->delete()
            ->execute();
                        
        $query = $curl->make('docs/tests/1');
        $response = $query
                        ->get()
                        ->execute();

        $this->assertNull($response);
    }
}