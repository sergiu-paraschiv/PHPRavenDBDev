<?php

namespace SergiuParaschiv\Raven\Util\Curl;

class Curl {
    
    private $url;
    private $session;
    private $reuseSession;
    
    public function __construct($url = '', $reuseSession = false)
    {
        $this->url = $url;
        $this->reuseSession = $reuseSession;
        
        if($this->reuseSession)
        {
            $this->session = new CurlSession($this->url);
        }
    }
    
    public function make($url)
    {
        return new CurlQuery($this->url . $url, $this->session);
    }
    
    public function close()
    {

        if(!$this->reuseSession)
        {
            throw new CurlException('Attempting to manually close a non-reusable session.');
        }
        
        $this->session->close();
    }
}