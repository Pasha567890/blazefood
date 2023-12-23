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
    <title>MealHappy - Главная</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        #map {
            width: 100%;
            height: 400px;
        }
    </style>
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
    <div class="container">
        <h1 class="mt-5">Добро пожаловать в ресторан MealHappy!</h1>
        <p>В нашем ресторане вы найдете вкуснейшие блюда быстрого приготовления. У нас вы можете насладиться блюдами высокого качества в комфортной и уютной обстановке. Мы предлагаем широкий выбор блюд для всех вкусов - от классических бургеров до экзотических деликатесов.</p>

        <div id="map"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.1/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=apikey&lang=ru_RU"></script>
    <script src="landing.js"></script>
</body>

</html>
