<?php
include_once '../db.php';
session_start();
header('Content-Type: application/json');

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $orders = getUserOrders($userId);
    echo json_encode(['success' => true, 'data' => $orders]);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deleteOrder') {
    $orderId = $_POST['orderId'] ?? null;

    if (checkDeleteOrder($userId, $orderId)) {
        if (deleteOrder($orderId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при удалении заказа']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Заказ не принадлежит пользователю']);
    }
    exit;
}
