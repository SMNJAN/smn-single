<?php
/**
 * Created by Kuhva.
 */
require 'vendor/autoload.php';
if ((new \smnjan\Auth())->isLoggedIn()){
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}