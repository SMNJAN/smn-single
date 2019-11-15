<?php
/**
 * Created by Kuhva.
 */
require '../vendor/autoload.php';

use smnjan\Auth;
use smnjan\User;

$auth = new Auth();
$auth->requireLogin();

if ($_POST['password'] === $_POST['password_repeat']){
    if(User::changePassword($_POST['password'])){
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "nomatch";
}




