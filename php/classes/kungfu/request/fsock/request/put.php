<?php

class Kungfu_Request_FSock_Request_Put extends Kungfu_Request_FSock_Request
{
    const METHOD = 'PUT';
    protected $options = array();
    
    public function __construct($url)
    {
        parent::__construct($url);
        
        $this->options = array(
            'http' => array(
            'method' => self::METHOD,
            'ignore_errors' => true
        ));
    }
    
    public function execute()
    {
        $options = $this->options;
        $options['http']['content'] = http_build_query($this->data);
        $url = $this->build_url();
    
        return $this->_execute($url, $options);
    }
}