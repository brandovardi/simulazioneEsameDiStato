$(document).ready(function () {
    // coordinate di Milano
    let lat = 45.4682012;
    let lng = 9.1821959;
    let zoom = 10;
    let map = L.map('map').setView([lat, lng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
        .openPopup();

    map.on('click', function (e) {
        let lat = e.latlng.lat;
        let lng = e.latlng.lng;

        console.log('You clicked the map at latitude: ' + lat + ' and longitude: ' + lng);
    });

});
