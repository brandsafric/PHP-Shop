<?php

namespace App\Models;


class User extends \Core\Model
{
    protected static $table = 'users';

    public static function isValidUsername($username)
    {
        return (preg_match('/^[a-z]+[a-z0-9_]{3,14}$/', $username));
    }

    public static function isValidPassword($password)
    {
        return (preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}/', $password));

    }
}
