<?php
/**
 * Created by Kuhva.
 */
namespace smnjan;

use PDO;

class Database
{
    private static $_dbconn;
    public static function getDB()
    {
        if (self::$_dbconn === NULL) {
            $dsn =  'mysql:host='.Config::DB_HOST.';dbname='.Config::DB_NAME.';charset=utf8mb4';
            self::$_dbconn = new PDO($dsn, Config::DB_USER,Config::DB_PSSWD);
            self::$_dbconn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        return self::$_dbconn;
    }
}