<?php
require_once __DIR__ . '/../src/config/connection.php';
use App\Middleware\AuthMiddleware;
use App\Controllers\UserController;

$user = AuthMiddleware::check();
$userController = new UserController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $userController->deleteUser($_POST['id']);
        header('Location: /index.php');
        exit;
    } elseif ($_POST['action'] === 'update' && isset($_POST['id'], $_POST['username'], $_POST['email'])) {
        $userController->updateUser($_POST['id'], $_POST['username'], $_POST['email']);
        header('Location: /index.php');
        exit;
    }
}

$users = $userController->getAllUsers();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($user['username']) ?></h1>
    <a href="/logout.php">Logout</a>

    <h2>User Management</h2>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= $u['created_at'] ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                    </form>
                    <button onclick="document.getElementById('edit-<?= $u['id'] ?>').style.display='block'">Edit</button>
                    
                    <div id="edit-<?= $u['id'] ?>" style="display:none; border:1px solid black; padding:10px; margin-top:5px;">
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <label>Username: <input type="text" name="username" value="<?= htmlspecialchars($u['username']) ?>" required></label><br>
                            <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" required></label><br>
                            <button type="submit">Save</button>
                            <button type="button" onclick="document.getElementById('edit-<?= $u['id'] ?>').style.display='none'">Cancel</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>