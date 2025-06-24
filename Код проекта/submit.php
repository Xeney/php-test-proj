<?php
require 'database.php';

header('Content-Type: application/json');

file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Нет подключения к БД']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$comment = trim($_POST['comment'] ?? '');

try {
    $tableExists = $pdo->query("SHOW TABLES LIKE 'submissions'")->rowCount() > 0;
    if (!$tableExists) {
        http_response_code(500);
        echo json_encode(['error' => 'Таблица submissions не существует']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO submissions (name, phone, comment) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phone, $comment]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Данные сохранены!',
        'id' => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка БД: ' . $e->getMessage()]);
}
?>