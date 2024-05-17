$(document).ready(async function () {
    let coords = await request("GET", "../../Controllers/Address/getCoords.php", {});
    let jsonCoords = JSON.parse(coords);
    coords = jsonCoords.coords;

    // coordinate di Milano
    let lat = coords.latitudine;
    let lng = coords.longitudine;
    let zoom = 15;
    let map = L.map('map').setView([lat, lng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    // map.on('click', function(e) {
    //     map.setView(e.latlng, 15);
    // });

    // .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
    // .openPopup();

    // map.on('click', function (e) {
    //     let lat = e.latlng.lat;
    //     let lng = e.latlng.lng;

    //     console.log('You clicked the map at latitude: ' + lat + ' and longitude: ' + lng);
    // });

});