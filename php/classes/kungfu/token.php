<?php

class Kungfu_Token
{
    protected $access;
    protected $refresh;
    
    public function __construct($access, $refresh)
    {
        $this->access = $access;
        $this->refresh = $refresh;
    }
    
    public function access()
    {
        return $this->access;
    }
    
    public function refresh()
    {
        return $this->refresh;
    }
}