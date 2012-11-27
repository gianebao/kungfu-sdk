<?php

class Kungfu_Request_FSock_Request extends Kungfu_Request_Factory
{
    protected static $name = 'Kungfu_Request_FSock_Request';
    
    protected function _execute($url, $options)
    {
        $context = stream_context_create($options);
        $fp = fopen($url, 'rb', false, $context);
        
        if (!$fp)
        {
           $body = false;
        }
        else
        {
           // If you're trying to troubleshoot problems, try uncommenting the
           // next two lines; it will show you the HTTP response headers across
           // all the redirects:
           $header = stream_get_meta_data($fp);
           $header = $header['wrapper_data'];
           $body = stream_get_contents($fp);
         }

        if ($body === false)
        {
          return trigger_error('failed: ['. $url . '] ' . $php_errormsg);
        }
        
        return array(
            'status' => '',
            'head' => self::parse_head($header),
            'result' => $body
        );
    }
    
    public function execute()
    {
        $url = $this->build_url($this->data);
    
        return $this->_execute($url, $this->options);
    }
}