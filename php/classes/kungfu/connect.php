<?php

class Kungfu_Connect
{
    const URL_AUTHORIZE = '/oauth2/authorize';
    const URL_PASSWORD = '/oauth2/connect';
    const URL_REFRESH = '/oauth2/token';
    
    const DEFAULT_DOMAIN = 'www.matchmove.com';
    const OAUTH_TOKEN = 'oauth_token';
    protected $url;
    
    public function __construct(& $url, $domain = null)
    {
        $this->url = $url;

        $this->domain = $this->url->protocol() . (empty($domain) ? self::DEFAULT_DOMAIN: $domain);
    }
    
    private static function clean_request($key)
    {
        if (!empty($_GET[$key]) && !empty($_POST[$key]))
        {
            trigger_error('Invalid `' . $key . '`.', E_USER_ERROR);
        }
        
        if (!empty($_GET[$key]))
        {
            return $_GET[$key];
        }
        elseif (!empty($_POST[$key]))
        {
            return $_POST[$key];
        }
        
        return false;
    }
    
    public static function get_state()
    {
        $state = self::clean_request('state');
        return Kungfu_Session::get('state') === $state ? $state: false;
    }
    
    public function get_sign()
    {
        $sign = self::clean_request('signed_request');
        
        return empty($sign) ? null: self::parse_signed_request($sign, $this->url->secret());
    }
    
    public function initialize()
    {
        $sign = Kungfu::get_access_credentials();
        
        /**
         * automatically tries to refresh the access token
         */
        if (!empty($sign))
        {
            $this->url->token(new Kungfu_Token($sign['access_token'], $sign['refresh_token']));
            return true;
        }
        
        $state = self::get_state();

        if (false !== $state && !empty($_GET['error']))
        {
            Kungfu_Session::kill('state');
            trigger_error('Authentication sends `' . $_GET['error'] . '`', E_USER_ERROR);
        }
        
        $sign = $this->get_sign();
        
        if (false === $state || empty($sign) || empty($sign['access_token']) || empty($sign['refresh_token']))
        {
            return false;
        }
        
        $this->url->token(new Kungfu_Token($sign['access_token'], $sign['refresh_token']));
        Kungfu::set_access_credentials($sign['access_token'], $sign['refresh_token']);
        
        return $sign;
    }
    
    public function authorize($redirect_uri = null, $query = array())
    {
        $url = $this->domain . self::URL_AUTHORIZE;
        
        $state = md5('statemmad!-' . uniqid());
        Kungfu_Session::set('state', $state);
        
        $query = array_merge($query, array(
            'client_id' => $this->url->key(),
            'response_type' => 'token',
            'state' => $state,
            'redirect_uri' => $this->url->redirect_uri($redirect_uri)
        ));
        
         $url .= '?' . http_build_query($query);
        
        Kungfu_Url::redirect($url);
    }
    
    public function password($username, $password)
    {
        $url = $this->domain . self::URL_PASSWORD;
        
        $data = array(
            'grant_type' => 'password',
            'client_id' => $this->url->key(),
            'client_secret' => $this->url->secret(),
            'username' => $username,
            'password' => $password
        );
        
        $sign = Kungfu_Request::factory('POST', $url)->data($data)->execute();
        
        if (empty($sign) || empty($sign['result']))
        {
             return trigger_error('Connect No response.', E_USER_ERROR);;
        }
        
        $sign = json_decode($sign['result'], true);
        
        if (empty($sign))
        {
            return trigger_error('Connect Invalid response', E_USER_ERROR);;
        }
        elseif (!empty($sign['error']) && 'invalid_grant' == $sign['error'])
        {
            return false; // its ok. invalid credentials only.
        }
        elseif (!empty($sign['error']))
        {
            return trigger_error('Connect Request responded `' . $sign['error'] . '`', E_USER_ERROR);
        }
        
        //$sign = self::parse_signed_request($sign['sign'], $this->url->secret());
        
        if (empty($sign) || empty($sign['access_token']) || empty($sign['refresh_token']))
        {
            return false;
        }
        
        $this->url->token(new Kungfu_Token($sign['access_token'], $sign['refresh_token']));
        Kungfu::set_access_credentials($sign['access_token'], $sign['refresh_token']);
        
        return $sign;
    }
    
    public function refresh($sign)
    {
        $url = $this->domain . self::URL_REFRESH;
        
        if (empty($sign))
        {
            return false;
        }
        
        $data = array(
            'grant_type' => 'refresh_token',
            'client_id' => $this->url->key(),
            'client_secret' => $this->url->secret(),
            'refresh_token' => $sign['refresh_token']
        );
        
        $sign = Kungfu_Request::factory('POST', $url)->data($data)->execute();
        
        if (empty($sign) || empty($sign['result']))
        {
             return trigger_error('Refresh No response.', E_USER_ERROR);;
        }
        
        $sign = json_decode($sign['result'], true);
        
        if (empty($sign))
        {
            return trigger_error('Refresh Invalid response', E_USER_ERROR);;
        }
        elseif (!empty($sign['error']))
        {
            return trigger_error('Refresh Request responded `' . $sign['error'] . '`', E_USER_ERROR);
        }
        
        //$sign = self::parse_signed_request($sign['sign'], $this->url->secret());
        
        if (empty($sign) || empty($sign['access_token']) || empty($sign['refresh_token']))
        {
            return false;
        }
        
        $this->url->token(new Kungfu_Token($sign['access_token'], $sign['refresh_token']));
        Kungfu::set_access_credentials($sign['access_token'], $sign['refresh_token']);
        
        return $sign;
    }
    
    private static function parse_signed_request($signed_request, $secret)
    {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
      
        // decode the data
        $sig = self::base64_url_decode($encoded_sig);
        $data = json_decode(self::base64_url_decode($payload), true);
      
        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256')
        {
            error_log('Unknown algorithm. Expected HMAC-SHA256');
            return null;
        }
      
        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig)
        {
            error_log('Bad Signed JSON signature!');
            return null;
        }
      
        return $data;
    }

    private static function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
    public function url()
    {
        return $this->url;
    }
    
    public static function header_is_expired(& $header)
    {
        return !empty($header['WWW-Authenticate']) && false !== strpos(strtolower($header['WWW-Authenticate']), 'expired_token');
    }
}