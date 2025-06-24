<?php
session_start();
require '../database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM submissions WHERE id = ?");
    $stmt->execute([$delete_id]);
    header('Location: dashboard.php');
    exit;
}

$sort = isset($_GET['sort']) && $_GET['sort'] === 'asc' ? 'ASC' : 'DESC';
$sort_icon = $sort === 'ASC' ? '↑' : '↓';
$reverse_sort = $sort === 'ASC' ? 'desc' : 'asc';

$stmt = $pdo->query("SELECT * FROM submissions ORDER BY created_at $sort");
$submissions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            cursor: pointer;
        }
        .delete-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #cc0000;
        }
        .sort-header {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <h1>Записи из формы</h1>
    
    <div style="margin-bottom: 15px;">
        <a href="?sort=<?= $reverse_sort ?>" class="sort-btn">
            Сортировать по дате: <?= $sort === 'DESC' ? 'Новые → Старые' : 'Старые → Новые' ?>
        </a>
    </div>

    <table>
        <tr>
            <th>Имя</th>
            <th>Телефон</th>
            <th>Сообщение</th>
            <th>
                <a href="?sort=<?= $reverse_sort ?>" class="sort-header">
                    Дата <?= $sort_icon ?>
                </a>
            </th>
            <th>Действия</th>
        </tr>
        <?php foreach ($submissions as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['comment']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <form method="GET" style="display: inline;">
                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                    <button type="submit" class="delete-btn" onclick="return confirm('Вы уверены?')">Удалить</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="logout.php">Выйти</a>
</body>
</html>