<?php

namespace Core;


class Data
{
    
    public static function userIsLoggedIn()
    {
        return isset ($_SESSION['user']);
    }    
    
    public static function IsUserAdmin()
    {
        return Data::userIsLoggedIn() && Auth::user()->role === 'admin';
    }

    public static function setUserData($id)
    {
        $_SESSION['user'] = $id;
    }

    public static function generateCSRFToken()
    {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(64));
        return $_SESSION['token'];
    }

    public static function sanitize($string, $force_lowercase = false, $anal = false)
    {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "-", strip_tags($string)));
//        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;

        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }
}