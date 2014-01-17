<?php

namespace SergiuParaschiv\Raven\Util\Curl;

class CurlSession {
    
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_NO_CONTENT = 204;
    const HTTP_NOT_FOUND = 404;

    private $url;
    private $session;
    private $method;
    private $headers;
    private $data;
    
    public function __construct()
    {
        $this->session = curl_init();
        $this->reset();
        
        return $this;
    }
    
    public function setURL($url)
    {
        $this->url = $url;
        
        return $this;
    }
    
    public function setMethod($method)
    {
        $this->method = $method;
    }
    
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }
    
    public function execute()
    {
        $method = $this->method;
        
        if($this->method === CurlQuery::METHOD_PUT)
        {
            $this->headers['X-HTTP-Method-Override'] = 'PUT';
        }
        
        curl_setopt($this->session, CURLOPT_URL, $this->url);
        curl_setopt($this->session, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($this->session, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->session, CURLOPT_POSTFIELDS, $this->data);

        $response = curl_exec($this->session);
        $info = curl_getinfo($this->session);
        
        $this->reset();
        
        if(
            $info['http_code'] === self::HTTP_OK
            || $info['http_code'] === self::HTTP_NO_CONTENT
            || $info['http_code'] === self::HTTP_CREATED
        ) {
            return $response;
        }
        else if(
            $info['http_code'] === self::HTTP_NOT_FOUND
            && $method === CurlQuery::METHOD_GET
        ){
            return null;
        }
        else {
            throw new CurlException('Server error code [' . $info['http_code'] . ']');
        }

        return null;
    }
    
    public function close()
    {
        curl_close($this->session);
    }
    
    private function reset()
    {
        $this->method = CurlQuery::METHOD_GET;
        $this->headers = [];
        $this->data = [];
        
        curl_setopt($this->session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->session, CURLOPT_FAILONERROR, true);
    }
}