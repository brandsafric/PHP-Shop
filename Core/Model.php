<?php

namespace Core;

use App\Models\Setting;
use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 5.4
 */
abstract class Model
{


    public static function getDB()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    public static function getNewInstance()
    {
        $obj = get_called_class();
        return new $obj();
    }

    public static function all($params = null)
    {
        $obj=self::getNewInstance();
        $db = DB::getDB();
        return $db->query("SELECT * FROM " . $obj::$table . " $params")->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public static function find($id)
    {
        $obj = self::getNewInstance();
        $db = DB::getDB();
        $newInstance = $db->query("SELECT * FROM {$obj::$table} WHERE id = '$id'")->fetchAll(PDO::FETCH_CLASS, get_called_class());
        return count ($newInstance) > 0 ? $newInstance[0] : null;
    }

    public function update()
    {
        try {
            $query = '';
            $execute_array = [];
            foreach ($this as $key => $index) {
                $query .= "$key = :$key, ";
                $execute_array[":$key"] = ($index != null ? $index : NULL);
            }
            $query = substr($query, 0, strlen($query) - 2);
            $obj = self::getNewInstance();
            $db = DB::getDB();

            $query = "UPDATE " . $obj::$table . " SET $query WHERE id = '$this->id'";
            $stmt = $db->prepare($query);
            $stmt->execute($execute_array);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public function save()
    {
        $db = DB::getDB();

        $values = $columns = '';
        $execute_array = [];
        foreach ($this as $key => $index){
            $columns .= "$key, ";
            $values .= ":$key, ";
            $execute_array[":$key"] = $index;
        }
        $columns = substr ($columns, 0, strlen($columns) - 2 );
        $values = substr ($values, 0, strlen($values) - 2 );

        $stmt = $db->prepare("INSERT INTO {$this::$table} ($columns) VALUES ($values)");
        $stmt->execute($execute_array);
        $this->id=$db->lastInsertId();
    }

    public function delete()
    {
        $db = DB::getDB();
        $db->query("DELETE FROM {$this::$table} WHERE id = {$this->id}");
    }

    public static function where($name, $value = null)
    {
        $obj = self::getNewInstance();
        $query = "SELECT * FROM " . $obj::$table . " WHERE $name " . ($value == null ? 'IS NULL' : "= '$value'");
        return $obj::getDB()->query($query)->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public static function first($name, $value)
    {
        $obj = self::getNewInstance();
        $db = DB::getDB();
        $newInstance = $db->query("SELECT * FROM {$obj::$table} WHERE $name = '$value'")->fetchAll(PDO::FETCH_CLASS, get_called_class());
        return count ($newInstance) > 0 ? $newInstance[0] : null;
    }

    public static function query($query)
    {
        try {
            $db = self::getDB();
            $stmt = $db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);
            return $result;
        }catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function queryToSpecificClass($query)
    {
        try {
            $db = self::getDB();
            $stmt = $db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
            return $result;
        }catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function query1($query)
    {
        try {
            $db = self::getDB();
            $stmt = $db->query($query);
        }catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function count($params = null)
    {
        $obj = self::getNewInstance();
        return self::query("SELECT COUNT(*) as count FROM {$obj::$table} $params")[0]->count;
    }

}
