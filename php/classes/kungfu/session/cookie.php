<?php

class Kungfu_Session_Cookie
{
    const SALT = 'arifer842.e5v49b84nc29137~EBYeheurbv47F';
    
    public static function set($id, $value)
    {
        $user_id = $id;
        $hash = sha1(rand(0,500) . microtime() . self::SALT);
        $signature = sha1(self::SALT . $hash . $value);
        
        $cookie = base64_encode($signature . '-' . $hash . '-' . $value);
        setcookie($id, $cookie);
    }
    
    public static function get($id)
    {
        if (empty($_COOKIE[$id]))
        {
            return null;
        }
        
        $data = explode('-', base64_decode($_COOKIE[$id]));
        
        if (3 !== count($data))
        {
            return false;
        }
        
        if ($data[0] !== sha1(self::SALT . $data[1] . $data[2]))
        {
            return false;
        }
        
        return $data[2];
    }

    public static function kill($id)
    {
        setcookie ($id, '', time() - 3600);
    }
}