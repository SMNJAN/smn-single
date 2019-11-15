<?php
/**
 * Created by Kuhva.
 */
namespace smnjan;


class Auth
{
    private $currentUser;
    public function __construct() {}

    /**
     * @param $password
     * @return bool
     */
    public function login($password)
    {
        $db = Database::getDB();
        $stmt = $db->prepare("SELECT * FROM `users` WHERE id = 1 LIMIT 1");
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user !== NULL && password_verify($password, $user['passwort'])) {
            $this->currentUser = $user;
            $_SESSION['user_id'] = $user['id'];
            session_regenerate_id();
            return true;
        }
        return false;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentUser()
    {
        if ($this->currentUser === NULL) {

            //Login from SESSION if set
            if (isset($_SESSION['user_id'])) {
                $this->currentUser = User::findbyID($_SESSION['user_id']);
            }
        }
        return $this->currentUser;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->getCurrentUser() !== null;
    }


    /**
     * @return void
     */
    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
        }
    }

    /**
     * @return void
     */
    public function logout()
    {
        $_SESSION = array();
        session_destroy();
    }
}