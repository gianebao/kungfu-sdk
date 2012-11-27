<?php

class Kungfu_Url
{
    const API_DOMAIN = KUNGFU_DOMAIN;
    const API_OUTPUT = 'json';
    protected $protocol = 'http://';
    protected $key;
    protected $secret;
    protected $redirect_uri;
    protected $version;
    protected $token;
    
    public function __construct($config)
    {
        if (!empty($config['ssl']) && true === $config['ssl'])
        {
            $this->protocol = 'https://';
        }
        
        $this->key = $config['key'];
        $this->secret = $config['secret'];
        $this->version = $config['version'];
        $this->redirect_uri = $config['redirect_uri'];
    }
    
    public function protocol()
    {
        return $this->protocol;
    }
    
    public function key()
    {
        return $this->key;
    }
    
    public function secret()
    {
        return $this->secret;
    }
    
    public function redirect_uri($uri = '')
    {
        return $this->redirect_uri . $uri;
    }
    
    public static function redirect($url)
    {
         header('Location: ' . $url);
    }
    
    public function token($token = null)
    {
        if (empty($token))
        {
            return $this->token;
        }
        
        $this->token = $token;
        
        return $this;
    }
    
    public function api($library)
    {
        $seg = array($this->protocol . self::API_DOMAIN);
        $seg[] = strtolower($library);
        $seg[] = $this->version;
        $seg[] = $this->key;
        $seg[] = self::API_OUTPUT;
        
        return implode('/', $seg);
    }
}