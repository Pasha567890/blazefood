$(document).ready(function () {
    function loadDishes() {
        $.ajax({
            url: 'index_mediator.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    displayDishes(response.data);

                    var cartItems = response.data.filter(function (dish) {
                        return dish.count > 0;
                    });

                    displayCart(cartItems);
                } else {
                    alert(response.error);
                }
            },
            error: function () {
                alert('Ошибка при загрузке блюд.');
            }
        });
    }

    function displayDishes(dishes) {
        var menuCards = $('#menu .menu-cards');
        menuCards.empty();

        dishes.forEach(function (dish) {
            var priceParts = dish.price.split('.');

            var buttonContent = dish.count === 0
                ? `<button id="add-${dish.id}" class="btn btn-block btn-grey">Добавить +</button>`
                : `<div class="btn-group btn-block" role="group">
                  <button id="decrease-${dish.id}" class="btn btn-grey">-</button>
                  <button id="count-${dish.id}" class="btn btn-grey" disabled>${dish.count}</button>
                  <button id="increase-${dish.id}" class="btn btn-grey">+</button>
               </div>`;

            var dishCard = $(
                `<div class="card">
                    <img src="${dish.image_src}" class="card-img-top" alt="${dish.name}">
                    <div class="card-body">
                        <div class="price">${priceParts[0]}<a class="decimal">,${priceParts[1]}₽</a></div>
                        <h5 class="card-title">${dish.name}</h5>
                        <p class="weight">${dish.weight} g</p>
                        ${buttonContent}
                    </div>
                </div>`
            );
            menuCards.append(dishCard);
        });
    }

    function displayCart(cartItems) {
        var cartContainer = $('#cart');
        cartContainer.empty();

        var header = $('<div class="cart-header d-flex justify-content-between align-items-center"></div>')
            .append('<h3>Корзина</h3>')
            .append('<button class="clear-cart-btn btn btn-light">Очистить</button>');
        cartContainer.append(header);

        if (cartItems.length === 0) {
            cartContainer.append('<p class="text-center">Ничего не добавлено!</p>');
        } else {
            cartItems.forEach(function (item) {
                var cartItem = $('<div class="cart-item d-flex align-items-center"></div>');
                cartItem.append('<img src="' + item.image_src + '" alt="' + item.name + '" class="img-fluid" style="max-width: 80px;">');
                var itemInfo = $('<div class="item-info"></div>')
                    .append('<p>' + item.name + ` (${item.count} шт.)` + '</p>')
                    .append('<p>' + item.price + '₽, ' + item.weight + 'г</p>');
                cartItem.append(itemInfo);
                cartContainer.append(cartItem);
            });

            var orderButton = $('<button class="btn btn-warning rounded-button">Оформить заказ</button>');
            cartContainer.append(orderButton);
        }
    }



    function updateDishCount(dishId, count) {
        $.ajax({
            url: 'index_mediator.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'updateDishCount',
                dishId: dishId,
                count: count
            },
            success: function (response) {
                if (response.success) {
                    loadDishes();
                } else {
                    alert(response.error);
                }
            },
            error: function () {
                alert('Ошибка при выполнении запроса.');
            }
        });
    }

    $('#cart').on('click', '.clear-cart-btn', function () {
        $.ajax({
            url: 'index_mediator.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'clearCart'
            },
            success: function (response) {
                if (response.success) {
                    loadDishes();
                    alert('Корзина очищена.');
                } else {
                    alert('В корзине нет блюд.');
                }
            },
            error: function () {
                alert('Ошибка при выполнении запроса.');
            }
        });
    });

    $('#cart').on('click', '.btn-warning', function () {
        $('#orderModal').modal('show');
    });

    $('#confirmOrder').on('click', function () {
        var tableNumber = $('#tableNumber').val();
        $.ajax({
            url: 'index_mediator.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'createOrder',
                tableNumber: tableNumber,
            },
            success: function (response) {
                if (response.success) {
                    alert('Заказ успешно оформлен!');
                    window.location.href = '../profile/profile.php';
                } else {
                    alert('Ошибка при оформлении заказа.');
                }
            },
            error: function () {
                alert('Ошибка при выполнении запроса.');
            }
        });
    });


    $('#menu').on('click', 'button[id^="add-"]', function () {
        var dishId = $(this).attr('id').split('-')[1];
        updateDishCount(dishId, 1);
    });

    $('#menu').on('click', 'button[id^="decrease-"]', function () {
        var dishId = $(this).attr('id').split('-')[1];
        var currentCount = parseInt($('#count-' + dishId).text());
        updateDishCount(dishId, currentCount - 1);
    });

    $('#menu').on('click', 'button[id^="increase-"]', function () {
        var dishId = $(this).attr('id').split('-')[1];
        var currentCount = parseInt($('#count-' + dishId).text());
        updateDishCount(dishId, currentCount + 1);
    });


    setInterval(loadDishes, 5000);
    loadDishes();
});
