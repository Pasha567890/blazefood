$(document).ready(function () {
    loadDishes();

    function loadDishes() {
        $.ajax({
            url: 'admin_mediator.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    displayDishes(response.data);
                } else {
                    alert('Ошибка: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка при загрузке блюд.');
            }
        });
    }

    function displayDishes(dishes) {
        var table = $('#dishes-table');
        table.empty();

        dishes.forEach(function (dish) {
            var row = `<tr>
                <td>${dish.id}</td>
                <td><input type="text" class="form-control name-input" value="${dish.name}"></td>
                <td><input type="number" class="form-control price-input" min="1" step="0.01" value="${dish.price}"></td>
                <td><input type="number" class="form-control weight-input" min="1" value="${dish.weight}"></td>
                <td><img src="${dish.image_src}" alt="${dish.name}" style="width: 50px; height: auto;"></td>
                <td>
                    <input type="file" class="d-none dish-image-input" data-dish-id="${dish.id}">
                    <button class="btn btn-primary change-image-btn" data-dish-id="${dish.id}">Изменить</button>
                </td>
                <td><button class="btn btn-danger delete-dish-btn" data-dish-id="${dish.id}">Удалить</button></td>
            </tr>`;
            table.append(row);
        });
    }

    $('#dishes-table').on('change', '.price-input', function () {
        var dishId = $(this).closest('tr').find('.delete-dish-btn').data('dish-id');
        var newPrice = $(this).val();

        $.ajax({
            url: 'admin_mediator.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'updateDishPrice',
                dishId: dishId,
                price: newPrice
            },
            success: function (response) {
                if (response.success) {
                    alert('Цена успешно обновлена.');
                    loadDishes();
                } else {
                    alert('Ошибка: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка при выполнении запроса.');
            }
        });
    });

    $('#dishes-table').on('change', '.name-input', function () {
        var dishId = $(this).closest('tr').find('.delete-dish-btn').data('dish-id');
        var newName = $(this).val();

        $.ajax({
            url: 'admin_mediator.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'updateDishName',
                dishId: dishId,
                name: newName
            },
            success: function (response) {
                if (response.success) {
                    alert('Название успешно обновлено.');
                    loadDishes();
                } else {
                    alert('Ошибка: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка при выполнении запроса.');
            }
        });
    });

    $('#dishes-table').on('change', '.weight-input', function () {
        var dishId = $(this).closest('tr').find('.delete-dish-btn').data('dish-id');
        var newWeight = $(this).val();

        $.ajax({
            url: 'admin_mediator.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'updateDishWeight',
                dishId: dishId,
                weight: newWeight
            },
            success: function (response) {
                if (response.success) {
                    alert('Вес успешно обновлен.');
                    loadDishes();
                } else {
                    alert('Ошибка: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка при выполнении запроса.');
            }
        });
    });

    $('#dishes-table').on('click', '.delete-dish-btn', function () {
        var dishId = $(this).data('dish-id');
        if (confirm('Вы уверены, что хотите удалить это блюдо?')) {
            $.ajax({
                url: 'admin_mediator.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'deleteDish',
                    dishId: dishId
                },
                success: function (response) {
                    if (response.success) {
                        alert('Блюдо успешно удалено.');
                        loadDishes();
                    } else {
                        alert('Ошибка: ' + response.error);
                    }
                },
                error: function () {
                    alert('Ошибка при выполнении запроса.');
                }
            });
        }
    });

    $('#dishes-table').on('click', '.change-image-btn', function () {
        $(this).siblings('.dish-image-input').trigger('click');
    });

    $('#dishes-table').on('change', '.dish-image-input', function () {
        var dishId = $(this).data('dish-id');
        var file = this.files[0];

        if (file) {
            var formData = new FormData();
            formData.append('action', 'changeImage');
            formData.append('dishId', dishId);
            formData.append('image', file);

            $.ajax({
                url: 'admin_mediator.php',
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        alert('Изображение успешно обновлено.');
                        loadDishes();
                    } else {
                        alert('Ошибка: ' + response.error);
                    }
                },
                error: function () {
                    alert('Ошибка при отправке изображения.');
                }
            });
        }
    });

    $('#add-dish-btn').on('click', function () {
        $('#add-dish-modal').modal('show');
    });

    $('#submit-add-dish').on('click', function () {
        var dishName = $('#dish-name').val().trim();
        var dishPrice = $('#dish-price').val().trim();
        var dishWeight = $('#dish-weight').val().trim();
        var dishImage = $('#dish-image')[0].files[0];

        if (!dishName || !dishPrice || !dishWeight || !dishImage) {
            alert('Все поля должны быть заполнены.');
            return;
        }

        var formData = new FormData();
        formData.append('name', dishName);
        formData.append('price', dishPrice);
        formData.append('weight', dishWeight);
        formData.append('image', dishImage);

        $.ajax({
            url: 'admin_mediator.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    loadDishes();
                    alert('Блюдо успешно добавлено.');
                    $('#add-dish-modal').modal('hide');
                } else {
                    alert('Ошибка: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка при выполнении запроса.');
            }
        });
    });


});
