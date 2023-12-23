<?php
function dbConnect()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mealhappy";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function userExists($login)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    $conn->close();
    return $exists;
}

function registerUser($login, $password)
{
    $conn = dbConnect();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (login, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $login, $hashed_password);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function loginUser($login, $password)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $stmt->close();
            $conn->close();
            session_start();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["login"] = $user["login"];
            return true;
        }
    }
    $stmt->close();
    $conn->close();
    return false;
}

function isAdmin()
{

    $userId = $_SESSION["user_id"];
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $isAdmin = $result->fetch_assoc()['admin'];
    $stmt->close();
    $conn->close();
    return $isAdmin;
}

function checkAuth()
{

    if (!isset($_SESSION["user_id"])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function checkNotAuth()
{

    if (isset($_SESSION["user_id"])) {
        header("Location: ../index/index.php");
        exit;
    }
}

function checkAdmin()
{
    if (!isAdmin()) {
        header("Location: ../index/index.php");
        exit;
    }
}

function getAvailableDishes($userId)
{
    $conn = dbConnect();

    $sql = "SELECT d.id, d.name, d.price, d.weight, d.image_src, i.count
            FROM dishes d
            LEFT JOIN (SELECT * FROM items WHERE user_id = ? AND order_id IS NULL) i
            ON d.id = i.dish_id
            ORDER BY d.id ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $dishes = [];

    while ($row = $result->fetch_assoc()) {
        $dishes[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'weight' => $row['weight'],
            'image_src' => $row['image_src'],
            'count' => $row['count'] ?? 0,
        ];
    }

    $stmt->close();
    $conn->close();

    return $dishes;
}

function updateDishCount($dishId, $userId, $count)
{
    $conn = dbConnect();

    $stmt = $conn->prepare("SELECT id FROM items WHERE dish_id = ? AND user_id = ? AND order_id IS NULL LIMIT 1");
    $stmt->bind_param("ii", $dishId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $foundItem = $result->fetch_assoc();
    $stmt->close();

    if (!$foundItem && $count > 0) {
        $stmt = $conn->prepare("INSERT INTO items (dish_id, user_id, count) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $dishId, $userId, $count);
    } elseif ($foundItem && $count <= 0) {
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $foundItem['id']);
    } elseif ($foundItem && $count > 0) {
        $stmt = $conn->prepare("UPDATE items SET count = ? WHERE id = ?");
        $stmt->bind_param("ii", $count, $foundItem['id']);
    }

    if ($stmt) {
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
}

function clearUserItems($userId)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("DELETE FROM items WHERE user_id = ? AND order_id IS NULL");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $success = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();
    return $success;
}

function createOrder($userId, $tableNumber)
{
    $conn = dbConnect();
    $sql = "INSERT INTO orders (user_id, delivery_date, seat) VALUES (?, DATE_ADD(NOW(), INTERVAL 1.5 MINUTE), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $tableNumber);
    $stmt->execute();
    $orderId = $stmt->insert_id;

    if ($orderId) {
        $updateSql = "UPDATE items SET order_id = ? WHERE user_id = ? AND order_id IS NULL";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $orderId, $userId);
        $updateStmt->execute();
        $updateStmt->close();
    }

    $stmt->close();
    $conn->close();

    return $orderId > 0;
}

function getUserOrders($userId)
{
    $conn = dbConnect();
    $sql = "SELECT o.id, o.seat, o.delivery_date, i.dish_id, i.count, d.name, d.image_src
            FROM orders o
            JOIN items i ON o.id = i.order_id
            JOIN dishes d ON i.dish_id = d.id
            WHERE o.user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[$row['id']]['seat'] = $row['seat'];
        $orders[$row['id']]['id'] = $row['id'];
        $orders[$row['id']]['delivery_date'] = $row['delivery_date'];
        $orders[$row['id']]['items'][] = [
            'name' => $row['name'],
            'count' => $row['count'],
            'image_src' => $row['image_src']
        ];
    }

    $stmt->close();
    $conn->close();

    return array_values($orders);
}

function deleteOrder($orderId)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $success = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();
    return $success;
}

function checkDeleteOrder($userId, $orderId)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $orderId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $isOrderBelongsToUser = $result->num_rows > 0;
    $stmt->close();
    $conn->close();
    return $isOrderBelongsToUser;
}

function getAllDishes()
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM dishes");
    $stmt->execute();
    $result = $stmt->get_result();
    $dishes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $dishes;
}

function deleteDish($dishId)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("DELETE FROM dishes WHERE id = ?");
    $stmt->bind_param("i", $dishId);
    $stmt->execute();
    $success = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();
    return $success;
}

function updateDishPrice($dishId, $newPrice)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("UPDATE dishes SET price = ? WHERE id = ?");
    $stmt->bind_param("di", $newPrice, $dishId);
    $stmt->execute();
    $success = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();
    return $success;
}

function updateDishName($dishId, $newName)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("UPDATE dishes SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $newName, $dishId);
    $stmt->execute();
    $success = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();
    return $success;
}

function updateDishWeight($dishId, $newWeight)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("UPDATE dishes SET weight = ? WHERE id = ?");
    $stmt->bind_param("ii", $newWeight, $dishId);
    $stmt->execute();
    $success = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();
    return $success;
}

function updateDishImage($dishId, $newImagePath)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("UPDATE dishes SET image_src = ? WHERE id = ?");
    $stmt->bind_param("si", $newImagePath, $dishId);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

function addDish($name, $price, $weight, $imagePath)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("INSERT INTO dishes (name, price, weight, image_src) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $name, $price, $weight, $imagePath);

    $result = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $result;
}
