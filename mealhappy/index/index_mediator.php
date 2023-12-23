<?php
include_once '../db.php';
header('Content-Type: application/json');

session_start();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $dishes = getAvailableDishes($userId);
    echo json_encode(['success' => true, 'data' => $dishes]);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateDishCount') {
    $dishId = $_POST['dishId'] ?? null;
    $count = $_POST['count'] ?? 0;

    if (!is_numeric($dishId) || !is_numeric($count)) {
        echo json_encode(['success' => false, 'error' => 'Неверные данные для запроса']);
        exit;
    }

    updateDishCount($dishId, $userId, $count);
    echo json_encode(['success' => true]);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clearCart') {
    $success = clearUserItems($userId);
    echo json_encode(['success' => $success]);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'createOrder') {
    $tableNumber = $_POST['tableNumber'] ?? null;

    if (!$tableNumber) {
        echo json_encode(['success' => false, 'error' => 'Неверные данные для запроса']);
        exit;
    }

    $success = createOrder($userId, $tableNumber);
    echo json_encode(['success' => $success]);
    exit;
}
