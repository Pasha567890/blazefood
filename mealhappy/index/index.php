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
    <title>MealHappy</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .menu-cards {
            display: flex;
            flex-wrap: wrap;
        }

        .card {
            border-radius: 45px;
            border-width: 4px;
            margin-right: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            flex: 1 1 calc(33.333% - 1rem);
        }

        .card .price {
            font-size: 1.5em;
            font-weight: bold;
        }

        .card .price .decimal {
            font-size: 0.75em;
            font-weight: normal;
            color: black;
            text-decoration: none;
        }

        .card .weight {
            color: grey;
        }

        .btn-grey {
            background-color: grey;
            color: white;
        }

        .shoplist {
            border-radius: 45px;
            border-width: 4px;
            margin-top: 3.5rem;
            padding: 1rem;
        }

        .cart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
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

    <div class="ml-4 mr-4">
        <div class="row justify-content-between">
            <!-- Раздел меню -->
            <div id="menu" class="col-8">
                <h1>Меню</h1>
                <div class="menu-cards">
                    <!-- Карточки с продуктами будут динамически добавляться с помощью JavaScript -->
                </div>
            </div>

            <!-- Корзина -->
            <div id="cart" class="card col-4 shoplist">
                <div class="cart-header"></div>
                <!-- Корзина с покупками и кнопка "Сделать заказ" -->
            </div>
        </div>
    </div>

    <div class="modal" id="orderModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Введите номер столика</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="number" min="1" max="100" id="tableNumber" class="form-control" placeholder="Номер столика">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirmOrder">Оформить заказ</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.1/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="index_ajax.js"></script>
</body>

</html>
