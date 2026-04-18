<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto as Symmetric;
use ParagonIE\HiddenString\HiddenString;

class JwtHelper {
    public static function generateToken($payload) {
        $jwtSecret = $_ENV['JWT_SECRET'] ;
        if (!$jwtSecret) {
            throw new \Exception('JWT secret not configured');
        }
        // Generate standard JWT
        $jwt = JWT::encode($payload, $jwtSecret, 'HS256');

        // Encrypt the JWT with Halite
        $encryptionKey = KeyFactory::loadEncryptionKey(__DIR__ . '/../../../.env.halite.key');
        $encryptedJwt = Symmetric::encrypt(
            new HiddenString($jwt),
            $encryptionKey
        );

        return $encryptedJwt;
    }

    public static function decodeToken($encryptedToken) {
        try {
            $encryptionKey = KeyFactory::loadEncryptionKey(__DIR__ . '/../../../.env.halite.key');
            
            // Decrypt the token back to a normal JWT string
            $decryptedJwt = Symmetric::decrypt(
                $encryptedToken,
                $encryptionKey
            );

            $jwtSecret = $_ENV['JWT_SECRET'] ;
            if (!$jwtSecret) {
                throw new \Exception('JWT secret not configured');
            }

            $decoded = JWT::decode($decryptedJwt->getString(), new Key($jwtSecret, 'HS256'));
            
            return (array) $decoded;
        } catch (\Exception $e) {
            throw new \Exception('Invalid or expired token');
        }
    }

    public static function setCookie($token) {
        setcookie('jwt_token', $token, time() + (86400 * 30), "/", "", false, true); // root path, HTTP-only true
    }

    public static function removeCookie() {
        setcookie('jwt_token', '', time() - 3600, "/");
    }

    public static function getTokenFromCookie() {
        return $_COOKIE['jwt_token'] ?? null;
    }
}
