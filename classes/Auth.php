<?php
class Auth {
    private $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; session_start(); }

    public function login($user, $pass) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username=?');
        $stmt->execute([$user]);
        $u = $stmt->fetch();
        if ($u && password_verify($pass, $u['password_hash'])) {
            $_SESSION['user_id'] = $u['id'];
            return true;
        }
        return false;
    }

    public function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php'); exit;
        }
    }

    public function userId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function logout() {
        session_destroy();
    }
}
