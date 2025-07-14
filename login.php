<?php
require 'vendor/autoload.php'; require 'classes/Auth.php';
$pdo = new PDO(...); $auth = new Auth($pdo);
if ($_POST) {
  if ($auth->login($_POST['user'], $_POST['pass'])) header('Location:index.php');
  else $err = "Invalid credentials";
}
?>
<!DOCTYPE html><html><head><title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container py-4">
  <h1>Login</h1>
  <?php if (isset($err)): ?><div class="alert alert-danger"><?= $err ?></div><?php endif; ?>
  <form method="post">
    <input name="user" class="form-control mb-2" placeholder="Username" required>
    <input name="pass" type="password" class="form-control mb-2" placeholder="Password" required>
    <button class="btn btn-primary">Login</button>
  </form>
</body></html>
