<?php
namespace App\Controllers;

use App\Helpers\JwtHelper;
use PDO;

class AuthController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($username, $password) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $payload = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'exp' => time() + 3600 // 1 hour
            ];
            
            $token = JwtHelper::generateToken($payload);
            JwtHelper::setCookie($token);
            
            return true;
        }
        
        return false;
    }
    
    public function register($username, $password, $email) {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password, email) VALUES (?, ?, ?)');
        return $stmt->execute([$username, $hash, $email]);
    }
    
    public function logout() {
        JwtHelper::removeCookie();
    }
}
