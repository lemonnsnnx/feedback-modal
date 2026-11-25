<?php
// Разрешаем CORS (если фронт и бэк на разных портах)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Получаем данные
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Нет данных']);
    exit;
}

$rating = $data['rating'] ?? 0;
$comment = $data['comment'] ?? '';

$host = 'localhost';
$port = '3307'; // ВАЖНО! На скриншоте видно, что порт 3307, а не стандартный 3306
$dbname = 'feeedback'; // Судя по папкам в DBeaver, база называется feedback (или коробки/reviews, проверьте точно)
$user = 'root';
$pass = ''; // ОСТАВЛЯЕМ ПУСТЫМ, как в DBeaver

try {
    // Обязательно добавляем порт в строку подключения, иначе PHP будет искать базу на порту 3306
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ... дальше ваш код INSERT ...

    // Подготовленный запрос (защита от SQL-инъекций)
    $stmt = $pdo->prepare("INSERT INTO feedback (rating, comment, created_at) VALUES (:rating, :comment, NOW())");
    $stmt->execute(['rating' => $rating, 'comment' => $comment]);

    // Возвращаем успех
    echo json_encode(['success' => true, 'data' => ['id' => $pdo->lastInsertId()]]);
} catch (PDOException $e) {
    // Если ошибка БД - возвращаем JSON, а не HTML!
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ошибка БД: ' . $e->getMessage()]);
}