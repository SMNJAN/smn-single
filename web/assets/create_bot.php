<?php
/**
 * Created by Kuhva.
 */
use smnjan\Auth;
require '../vendor/autoload.php';
$auth = new Auth();
$auth->requireLogin();

if (isset($_POST['nickname'],$_POST['server'],$_POST['serverselect'])){
    $newnode = intval($_POST['serverselect']);
    $bot = new smnjan\Bot($newnode);
    $name = $_POST['nickname'];
    $name = strip_tags($name);
    $nickname = $name;
    $serverpw = null;
    $server = $_POST['server'];
    if(isset($_POST['name'])){
        if ($_POST['name'] !== ""){
            $nickname = strip_tags($_POST['name']);
        }
    }
    if(isset($_POST['server_password'])){
        if ($_POST['server_password'] !== ""){
            $nickname = $_POST['server_password'];
        }
    }
    if ($bot->createbot($name,$server,$nickname,$serverpw)){
        echo "success";
    } else {
        echo "false";
    }

}