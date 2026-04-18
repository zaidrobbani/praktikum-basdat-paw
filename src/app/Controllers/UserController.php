<?php
namespace App\Controllers;

use App\Helpers\JwtHelper;
use PDO;

class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllUsers() {
        $stmt = $this->pdo->query('SELECT id, username, email, created_at FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $username, $email) {
        $stmt = $this->pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
        return $stmt->execute([$username, $email, $id]);
    }

    public function deleteUser($id) {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        $result = $stmt->execute([$id]);

        if ($result) {
            $token = JwtHelper::getTokenFromCookie();
            if ($token) {
                $decoded = JwtHelper::decodeToken($token);
                if ($decoded && isset($decoded['user_id']) && $decoded['user_id'] == $id) {
                    JwtHelper::removeCookie();
                }
            }
        }

        return $result;
    }
}
