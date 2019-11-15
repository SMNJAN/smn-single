<?php
/**
 * Created by Kuhva.
 */
require 'vendor/autoload.php';
if ((new \smnjan\Auth())->isLoggedIn()){
    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}