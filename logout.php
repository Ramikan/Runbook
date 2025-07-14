<?php require 'vendor/autoload.php'; require 'classes/Auth.php';
$pdo = new PDO(...); $auth = new Auth($pdo); $auth->logout();
header('Location:login.php');
