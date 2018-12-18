<?php
/**
 * Created by PhpStorm.
 * User: blade
 * Date: 4.9.2018 г.
 * Time: 13:47
 */

namespace Core;


use App\Models\User;

class Auth
{

    public static function user()
    {
        if (Data::userIsLoggedIn()){
            $user = User::find($_SESSION['user']);
            unset($user->remember_token);
            unset($user->password);
            return $user;
        }
    }

    public static function isAdmin()
    {
        return Data::userIsLoggedIn() && User::find($_SESSION['user'])->role=='admin';
    }

    public static function logOut()
    {
        unset ($_SESSION['user']);
        if (isset($_COOKIE['remember_me'])){
            unset($_COOKIE['remember_me']);
            setcookie('remember_me', null, -1);
        }
    }
}