<?php
$pdo = new PDO('mysql:host=localhost;dbname=mitre','root','');
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)")->execute(['admin', $hash]);
