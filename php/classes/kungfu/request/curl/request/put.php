<?php

class Kungfu_Request_CUrl_Request_Put extends Kungfu_Request_CUrl_Request
{
    
    protected function set_curl_options()
    {
        $this->options = array(
            CURLOPT_URL            => $this->build_url(),
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => count($this->data),
            CURLOPT_POSTFIELDS     => http_build_query($this->data),
            CURLOPT_CUSTOMREQUEST  => 'PUT',
        );
    }
}