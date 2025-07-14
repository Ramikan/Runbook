<?php
require 'vendor/autoload.php'; require 'classes/Auth.php';
$pdo = new PDO(...); $auth = new Auth($pdo); $auth->requireLogin();
if ($_POST['html'] && $_POST['apt']) {
  $stmt = $pdo->prepare("INSERT INTO saved_runbooks(user_id, apt, tactics, runbook_html) VALUES (?, ?, ?, ?)");
  $stmt->execute([$auth->userId(), $_POST['apt'], json_encode($_POST['tactics']), $_POST['html']]);
}
header('Location:history.php');
