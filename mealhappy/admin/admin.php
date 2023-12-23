<?php
include_once '../db.php';
session_start();
checkAuth();
checkAdmin();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Административная панель</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <header class="navbar navbar-expand-lg navbar-light bg-light justify-content-between">
        <a href="../landing/landing.php" class="navbar-brand">🌍 MealHappy</a>
        <a href="../index/index.php" class="navbar-brand">🍔 Заказать</a>
        <a href="../profile/profile.php" class="navbar-brand">👤 Личный кабинет</a>
        <a href="../admin/admin.php" class="navbar-brand">⚙️ Админ панель</a>
    </header>
    <div class="container mt-4">
        <h1>Административная панель</h1>
        <button id="add-dish-btn" class="btn btn-success">Добавить новое блюдо</button>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Вес</th>
                    <th>Текущая картинка</th>
                    <th>Изменение картинки</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody id="dishes-table">
                <!-- Контент таблицы через JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Фрагмент HTML для диалогового окна -->
    <div id="add-dish-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить новое блюдо</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-dish-form">
                        <div class="form-group">
                            <label for="dish-name">Название</label>
                            <input type="text" class="form-control" id="dish-name" required>
                        </div>
                        <div class="form-group">
                            <label for="dish-price">Цена</label>
                            <input type="number" class="form-control" id="dish-price" min="0.01" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="dish-weight">Вес</label>
                            <input type="number" class="form-control" id="dish-weight" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="dish-image">Изображение</label>
                            <input type="file" class="form-control-file" id="dish-image" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="submit-add-dish">Добавить новое блюдо</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.1/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="admin_ajax.js"></script>
</body>

</html>
