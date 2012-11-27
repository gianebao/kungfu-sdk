<?php

class Kungfu_Request_FSock_Request_Delete extends Kungfu_Request_FSock_Request
{
    const METHOD = 'DELETE';
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
}