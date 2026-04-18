<?php
namespace App\Middleware;

use App\Helpers\JwtHelper;

class AuthMiddleware {
    public static function check() {
        $token = JwtHelper::getTokenFromCookie();
        
        if (!$token) {
            header('Location: /login.php');
            exit();
        }

        $decoded = JwtHelper::decodeToken($token);

        if (!$decoded) {
            JwtHelper::removeCookie();
            header('Location: /login.php');
            exit();
        }

        return $decoded;
    }
    
    public static function redirectIfAuthenticated() {
        $token = JwtHelper::getTokenFromCookie();
        if ($token) {
            $decoded = JwtHelper::decodeToken($token);
            if ($decoded) {
                header('Location: /index.php');
                exit();
            }
        }
    }
}
