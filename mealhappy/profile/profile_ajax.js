$(document).ready(function () {
    function loadOrders() {
        $.ajax({
            url: 'profile_mediator.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    displayOrders(response.data);
                    equalizeOrderHeights();
                } else {
                    alert('Ошибка: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка при загрузке заказов.');
            }
        });
    }

    function displayOrders(orders) {
        var ordersContainer = $('#orders-container');
        ordersContainer.empty();

        orders.forEach(function (order) {
            var orderCard = $('<div class="col-md-6 mb-4"><div class="card"><div class="card-body"></div></div></div>');
            var cardBody = orderCard.find('.card-body');

            order.items.forEach(function (item) {
                var itemRow = $('<div class="d-flex align-items-center mb-2"></div>');
                itemRow.append('<img src="' + item.image_src + '" alt="' + item.name + '" class="img-fluid" style="max-width: 50px; margin-right: 10px;">');
                itemRow.append('<p>' + item.name + ' (' + item.count + ' шт.)</p>');
                cardBody.append(itemRow);
            });

            cardBody.append('<p>Будет доставлено: ' + order.delivery_date + '</p>');
            cardBody.append('<p>Столик №' + order.seat + '</p>');
            cardBody.append('<button class="btn btn-danger delete-order" data-order-id="' + order.id + '">Удалить заказ</button>');

            ordersContainer.append(orderCard);
        });
    }

    function equalizeOrderHeights() {
        var maxHeight = 0;
        $('.card').each(function () {
            var height = $(this).height();
            if (height > maxHeight) {
                maxHeight = height;
            }
        });

        $('.card').height(maxHeight);
    }

    $('#orders-container').on('click', '.delete-order', function () {
        var orderId = $(this).data('order-id');
        $.ajax({
            url: 'profile_mediator.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'deleteOrder',
                orderId: orderId
            },
            success: function (response) {
                if (response.success) {
                    alert('Заказ успешно удален.');
                    loadOrders();
                } else {
                    alert('Ошибка: ' + response.error);
                }
            },
            error: function () {
                alert('Ошибка при выполнении запроса.');
            }
        });
    });

    loadOrders();
    setInterval(loadOrders, 5000);
});
