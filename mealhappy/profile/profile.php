<?php
include_once '../db.php';
session_start();
checkAuth();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - MealHappy</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <header class="navbar navbar-expand-lg navbar-light bg-light justify-content-between">
        <a href="../landing/landing.php" class="navbar-brand">🌍 MealHappy</a>
        <a href="../index/index.php" class="navbar-brand">🍔 Заказать</a>
        <a href="../profile/profile.php" class="navbar-brand">👤 Личный кабинет</a>
        <?php if (isAdmin()) : ?>
            <a href="../admin/admin.php" class="navbar-brand">⚙️ Админ панель</a>
        <?php endif; ?>
    </header>
    <div class="container mt-4">
        <h1>Заказы пользователя <?php echo $_SESSION["login"] ?></h1>
        <a href="../auth/logout.php" class="btn btn-danger mb-4">Выйти из аккаунта</a>
        <div id="orders-container" class="row">
            <!-- Здесь будут отображаться заказы -->
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.1/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="profile_ajax.js"></script>
</body>

</html>
