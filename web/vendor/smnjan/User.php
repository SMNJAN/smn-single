<?php
/**
 * Created by Kuhva.
 */

namespace smnjan;

use PDOException;

class User
{

    public $errors;

    /**
     * @return mixed|null
     */
    public static function findByID($id)
    {
        try {
            $dbconn = Database::getDB();

            $stmt = $dbconn->prepare('SELECT * FROM `users` WHERE `id` = 1 LIMIT 1');
            $stmt->execute();
            $user = $stmt->fetch();
            if ($user !== false) {
                return $user;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
        return null;
    }

    /**
     * @param $password
     * @return bool
     */
    public static function changePassword($password)
    {
        try {
            $dbconn = Database::getDB();
            $stmt = $dbconn->prepare('UPDATE users SET `passwort` = :password WHERE id = 1 LIMIT 1');
            $stmt->bindValue(':password',password_hash($password, PASSWORD_DEFAULT));
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                return true;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
        return false;
    }

    public static function updateQP($array){
        try {
            $dbconn = Database::getDB();
            $stmt = $dbconn->prepare('UPDATE users SET `stream_quickplay` = :quickplay WHERE id = 1 LIMIT 1');
            $stmt->bindValue(':quickplay',json_encode($array));
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                return true;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
        return false;
    }
}