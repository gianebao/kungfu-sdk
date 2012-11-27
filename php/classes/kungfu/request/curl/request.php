<?php

class Kungfu_Request_CUrl_Request extends Kungfu_Request_Factory
{
    protected static $name = 'Kungfu_Request_CUrl_Request';
    
    private static $options_ssl = array();
    
    public function __construct($url)
    {
        parent::__construct($url);
        if (empty(self::$options_ssl))
        {
            self::$options_ssl = array(
                CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
                CURLOPT_SSL_VERIFYPEER  => true,
                CURLOPT_SSL_VERIFYHOST  => 2,
                CURLOPT_CAINFO          => KUNGFU_CERT . '/matchmove.crt',
            );
        }
    }

    protected function set_curl_options()
    {
        $this->options = array();
    }
    
    public function execute()
    {
        // we fork the process so we don't have to wait for a timeout 
        /**
        
        $pid = pcntl_fork();
        
        if (-1 == $pid)
        { 
            return false; //could not fork
        }
        
        if (empty($pid))
        {
            // we are the child
            while (microtime(true) < $expire)
            {
                sleep(0.5);
            }
            return FALSE;
        }
        **/
        
        // we are the parent 
        $ch = curl_init(); 
        $this->set_curl_options();
        
        if (0 === strpos(strtolower($this->exec_url), 'https://'))
        {
            $this->options = array_merge($this->options, self::$options_ssl);
        }
        
        curl_setopt_array($ch, $this->options); 
        $response = curl_exec($ch);
        
        $info = curl_getinfo($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $header = substr($response, 0, $info['header_size']);
        
        if (0 < (int) $info['download_content_length'])
        {
            $body = substr($response, -$info['download_content_length']);
        }
        else
        {
            $body = substr($response, $info['header_size']);
        }
        
        
        if (empty($response))
        {
            return FALSE;
        }
        
        return array(
            'status' => $status,
            'head' => self::parse_head($header),
            'result' => $body
        );
    }
}