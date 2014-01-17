<?php

namespace SergiuParaschiv\Raven\Util\Curl;

class CurlQuery {
    
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    
    private $url;
    private $session;
    private $reuseSession;
    private $method;
    private $headers;
    private $data;
    
    public function __construct($url = '', $session = null)
    {
        $this->reuseSession = false;
        $this->url = $url;
        $this->session = $session;
        $this->method = self::METHOD_GET;
        $this->headers = [];
        $this->data = null;

        if(!is_null($this->session))
        {
            $this->reuseSession = true;
        }
    }
    
    public function get()
    {
        $this->method = self::METHOD_GET;
        
        return $this;
    }
    
    public function put($data = null)
    {
        $this->method = self::METHOD_PUT;
        
        if(!is_null($data))
        {
            $this->data = $data;
        }
        
        return $this;
    }
    
    public function post($data = null)
    {
        $this->method = self::METHOD_POST;
        
        if(!is_null($data))
        {
            $this->data = $data;
        }
        
        return $this;
    }
    
    public function delete()
    {
        $this->method = self::METHOD_DELETE;
        
        return $this;
    }
    
    public function header($name, $value)
    {
        $this->headers[$name] = $name . ': ' . $value ;
        
        return $this;
    }
    
    public function removeHeader($name)
    {
        unset($this->headers[$name]);
        
        return $this;
    }
    
    public function execute()
    {
        if(!$this->reuseSession)
        {
            $this->session = new CurlSession();
        }
        
        $this->session->setURL($this->url);
        $this->session->setMethod($this->method);
        $this->session->setHeaders($this->headers);
        $this->session->setData($this->data);
        
        $response = $this->session->execute();
        
        if(!$this->reuseSession)
        {
            $this->session->close();
        }
        
        return $response;
    }
}