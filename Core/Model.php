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

    public static function getTableName()
    {
        $obj = get_called_class();
        $table_name = $obj::$table;
        return $table_name;
    }

    public static function getNewInstance()
    {
        $obj = get_called_class();
        return new $obj();
    }


    public static function all($params = null)
    {
        $table_name = self::getTableName();
        $db = DB::getDB();
        return $db->query("SELECT * FROM $table_name $params")->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public static function find($id)
    {
        $db = DB::getDB();
        $table_name = self::getTableName();
        $newInstance = $db->query("SELECT * FROM $table_name WHERE id = '$id'")->fetchAll(PDO::FETCH_CLASS, get_called_class());
        return count($newInstance) > 0 ? $newInstance[0] : null;
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
            $db = DB::getDB();

            $query = "UPDATE " . $this::$table . " SET $query WHERE id = '$this->id'";
            $stmt = $db->prepare($query);
            $stmt->execute($execute_array);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function save()
    {
        $db = DB::getDB();

        $values = $columns = '';
        $execute_array = [];
        foreach ($this as $key => $index) {
            $columns .= "$key, ";
            $values .= ":$key, ";
            $execute_array[":$key"] = $index;
        }
        $columns = substr($columns, 0, strlen($columns) - 2);
        $values = substr($values, 0, strlen($values) - 2);

        $stmt = $db->prepare("INSERT INTO {$this::$table} ($columns) VALUES ($values)");
        $stmt->execute($execute_array);
        $this->id = $db->lastInsertId();
    }

    public function delete()
    {
        $db = DB::getDB();
        $db->query("DELETE FROM {$this::$table} WHERE id = {$this->id}");
    }

    public static function where($name, $value = null)
    {
        $table_name = self::getTableName();
        $query = "SELECT * FROM $table_name WHERE $name " . ($value == null ? 'IS NULL' : "= '$value'");
        $obj = self::getNewInstance();
        return $obj::getDB()->query($query)->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public static function first($name, $value)
    {
        $table_name = self::getTableName();
        $db = DB::getDB();
        $newInstance = $db->query("SELECT * FROM $table_name WHERE $name = '$value' LIMIT 1")->fetchAll(PDO::FETCH_CLASS, get_called_class());
        return count($newInstance) > 0 ? $newInstance[0] : null;
    }

    public static function query($query)
    {
        try {
            $db = self::getDB();
            $stmt = $db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);
            return $result;
        } catch (PDOException $e) {
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
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function query1($query)
    {
        try {
            $db = self::getDB();
            $stmt = $db->query($query);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function count($params = null)
    {
        $table_name = self::getTableName();
        return self::query("SELECT COUNT(*) as count FROM $table_name $params")[0]->count;
    }

}
