<?php
session_start();
require '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && hash('sha256', $password) === $user['password']) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>

<form method="POST">
    <input type="text" value="admin" name="username" placeholder="Логин" required>
    <input type="password" value="970970" name="password" placeholder="Пароль" required>
    <button type="submit">Войти</button>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
</form>