<?php
require_once __DIR__ . '/../src/config/connection.php';
use App\Controllers\AuthController;

$auth = new AuthController($pdo);
$auth->logout();

header('Location: /login.php');
exit();
