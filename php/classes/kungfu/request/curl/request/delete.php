<?php

class Kungfu_Request_CUrl_Request_Delete extends Kungfu_Request_CUrl_Request
{
    
    protected function set_curl_options()
    {
        $this->options = array(
            CURLOPT_URL            => $this->build_url($this->data),
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
        );
    }
}