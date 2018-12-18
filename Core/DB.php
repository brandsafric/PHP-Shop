<?php
/**
 * Created by PhpStorm.
 * Users: blade
 * Date: 9.8.2018 г.
 * Time: 15:59
 */

namespace Core;

use \Core\Model;
use \App\Config;
use PDO;

class DB
{
    public static function getDB()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' .
                Config::DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    public static function query($query)
    {
        try {
            $db = self::getDB();
            $stmt = $db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

}