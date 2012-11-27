<?php

class Kungfu_Request_Factory
{
    protected $data = array();
    protected $auth_token = null;
    protected $url = '';
    protected $options = array();
    protected $exec_url = '';
    
    public static function factory($method, $url)
    {
        $instance = new ReflectionClass(Kungfu_Request::$name . '_' . $method);
        return $instance->newInstanceArgs(array($url));
    }
    
    public function __construct($url)
    {
        $this->url = $url;
    }
    
    public function data($data)
    {
        $this->data = $data;
        
        return $this;
    }
    
    public function auth_token($auth_token)
    {
        $this->auth_token = $auth_token;
        
        return $this;
    }
    
    public function build_url($data = array())
    {
        $data = array_merge($data, array(Kungfu_Connect::OAUTH_TOKEN => $this->auth_token));
        $this->exec_url = $this->url . '?' . http_build_query($data);
        return $this->exec_url;
    }
    
    public function url()
    {
        return $this->exec_url;
    }
    
    protected static function parse_head($head)
    {
        $template = array(
            'Date' =>  null,
            'WWW-Authenticate' =>  null,
            'Content-Type' =>  null,
        );
        
        if (!is_array($head))
        {
            $head = trim($head);
            $head = explode("\n", $head);
        }
        
        for ($i = 0, $count = count($head); $i < $count; $i++)
        {
            foreach ($template as $index => $value)
            {
                if (0 === strpos($head[$i], $index . ':'))
                {
                    $template[$index] = trim(substr($head[$i], strlen($index) + 2));
                }
            }
        }
        return $template;
    }
}