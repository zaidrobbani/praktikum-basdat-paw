<?php
require_once __DIR__ . '/../src/config/connection.php';
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;

AuthMiddleware::redirectIfAuthenticated();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';

    $auth = new AuthController($pdo);
    if ($auth->register($username, $password, $email)) {
        header('Location: /login.php');
        exit;
    } else {
        $error = "Registration failed. Username or email may already exist.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">Register</button>
    </form>
    <a href="/login.php">Login</a>
</body>
</html>