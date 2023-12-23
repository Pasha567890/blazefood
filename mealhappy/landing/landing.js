$(document).ready(function () {
    ymaps.ready(init);

    function init() {
        var myMap = new ymaps.Map("map", {
            center: [59.9342802, 30.3350986],
            zoom: 10
        });

        var myPlacemark = new ymaps.Placemark([59.9342802, 30.3350986], {
            hintContent: 'Ресторан MealHappy',
            balloonContent: 'Лучший ресторан быстрого питания в городе!'
        });

        myMap.geoObjects.add(myPlacemark);
    }
});
