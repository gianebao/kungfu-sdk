<?php
class Kungfu_Core
{
    protected $config = array();
    public $connect = null;
    public $last_request = array();
    
    public static function config($file)
    {
        if (is_array($file))
        {
            return $file;
        }
        
        $file = explode('.', $file);
        
        $config_file = KUNGFU_CONFIG . '/' . array_shift($file) . '.php';
        
        $config = include $config_file;
        
        if (empty($file))
        {
            return $config;
        }
        
        do
        {
            $index = array_shift($file);
            if (empty($config[$index]))
            {
                return trigger_error('`' . $index . '` does not exist in `' . $config_file . '`', E_USER_ERROR);
            }
            $config = $config[$index];
        } while (!empty($file));
        
        return $config;
        
    }
    
    public static function auto_load($class)
    {
        $file = KUNGFU_CLASS . '/' . str_replace('_', '/', strtolower($class)) . '.php';
        
        if (!file_exists($file))
        {
            return trigger_error('`' . $class . '` cannot be found', E_USER_ERROR);
        }
        
        include $file;
        
        if (!class_exists($class))
        {
            return trigger_error('`' . $class . '` does not exist', E_USER_ERROR);
        }
    }
    
    public static function str_file($file)
    {
        return str_replace(KUNGFU_ROOT, '..', $file);
    }
    
    public static function error_handle($errno, $errstr, $errfile = null, $errline = null, $errcontext = null)
    {
        die('<b>Fatal error:</b> ' . $errstr . ' in <b>' . self::str_file($errfile) . '</b> on line <b>' . $errline . '</b>');
    }
    
    
    public function __construct($domain = null, $config = null)
    {
        if (empty($config))
        {
            $config = 'kungfu.default';
        }
        
        $url = new Kungfu_Url(self::config($config));
        
        $this->connect = new Kungfu_Connect($url, $domain);
    }
    
    protected function before()
    {}
    
    public function read($api, $data = array(), $ignore_token = false)
    {
        $this->before();
        
        $url = $this->connect->url();
        $request = Kungfu_Request::factory('GET', $url->api($api))->data($data);
        
        $token = $url->token();
        if (!empty($token) && !$ignore_token)
        {
            $request = $request->auth_token($token->access());
        }
        
        $this->last_request = $request;
        
        $response = $request->execute();
        
        return $this->output($response);
    }
    
    public function create($api, $data)
    {
        $this->before();
        
        $url = $this->connect->url();
        $request = Kungfu_Request::factory('POST', $url->api($api))->data($data);
        
        $token = $url->token();
        if (!empty($token))
        {
            $request = $request->auth_token($token->access());
        }
        
        $this->last_request = $request;
        
        $response = $request->execute();
        
        return $this->output($response);
    }
    
    public function update($api, $data)
    {
        $this->before();
        
        $url = $this->connect->url();
        $request = Kungfu_Request::factory('PUT', $url->api($api))->data($data);
        
        $token = $url->token();
        if (!empty($token))
        {
            $request = $request->auth_token($token->access());
        }
        
        $this->last_request = $request;
        
        $response = $request->execute();
        
        return $this->output($response);
    }
    
    public function delete($api, $data)
    {
        $this->before();
        
        $url = $this->connect->url();
        $request = Kungfu_Request::factory('DELETE', $url->api($api))->data($data);
        
        $token = $url->token();
        if (!empty($token))
        {
            $request = $request->auth_token($token->access());
        }
        
        $this->last_request = $request;
        
        $response = $request->execute();
        
        return $this->output($response);
    }
    
    private function output(& $response)
    {
        if (false === $response)
        {
            return false;
        }
        
        if (Kungfu_Connect::header_is_expired($response['head']))
        {
            
            $sign = Kungfu::get_access_credentials();
            Kungfu_Session::kill('access');
            $this->connect->refresh($sign);
            $url = $this->connect->url();
            
            $response = $this->last_request->auth_token($url->token()->access())->execute();
        }
        
        $response['result'] = json_decode($response['result'], true);
        
        return $response;
    }
    
    public static function set_access_credentials($access_token, $refresh_token)
    {
        Kungfu_Session::set('access', json_encode(array('access_token' => $access_token, 'refresh_token' => $refresh_token)));
    }
    
    public static function get_access_credentials()
    {
        $access = Kungfu_Session::get('access');
        return json_decode($access, true);
    }
}