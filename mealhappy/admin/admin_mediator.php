<?php
include_once '../db.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $dishes = getAllDishes();
    echo json_encode(['success' => true, 'data' => $dishes]);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && isset($_POST['name']) && isset($_POST['price']) && isset($_POST['weight'])) {
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $weight = $_POST['weight'] ?? '';
        $image = $_FILES['image'];

        if (!$name || !$price || !$weight || !$image) {
            echo json_encode(['success' => false, 'error' => 'Некорректные данные']);
            exit;
        }
        $uploadDir = '../img/';
        $imagePath = $uploadDir . basename($image['name']);
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            if (addDish($name, $price, $weight, $imagePath)) {
                echo json_encode(['success' => true]);
                exit;
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при добавлении блюда']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при загрузке изображения']);
            exit;
        }
        exit;
    }

    if (isset($_FILES['image'])) {
        $dishId = $_POST['dishId'];
        $image = $_FILES['image'];
        $targetPath = "../img/" . basename($image["name"]);
        if (move_uploaded_file($image["tmp_name"], $targetPath)) {
            if (updateDishImage($dishId, $targetPath)) {
                echo json_encode(['success' => true]);
                exit;
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении изображения']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при загрузке файла']);
            exit;
        }
        exit;
    }

    if ($_POST['action'] === 'updateDishPrice') {
        $dishId = $_POST['dishId'];
        $newPrice = $_POST['price'];
        $success = updateDishPrice($dishId, $newPrice);

        echo json_encode(['success' => $success]);
        exit;
    }

    if ($_POST['action'] === 'updateDishName') {
        $dishId = $_POST['dishId'];
        $newName = $_POST['name'];
        $success = updateDishName($dishId, $newName);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($_POST['action'] === 'updateDishWeight') {
        $dishId = $_POST['dishId'];
        $newWeight = $_POST['weight'];
        $success = updateDishWeight($dishId, $newWeight);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($_POST['action'] === 'deleteDish') {
        $dishId = $_POST['dishId'];
        $success = deleteDish($dishId);
        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Неверный запрос']);
    exit;
}
