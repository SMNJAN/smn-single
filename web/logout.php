<?php
/**
 * Created by Kuhva.
 */
use smnjan\Auth;
require 'vendor/autoload.php';
(new Auth())->logout();
header('Location: login.php');