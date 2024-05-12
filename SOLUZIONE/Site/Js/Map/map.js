$(document).ready(function () {

    // .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
    // .openPopup();

    // map.on('click', function (e) {
    //     let lat = e.latlng.lat;
    //     let lng = e.latlng.lng;

    //     console.log('You clicked the map at latitude: ' + lat + ' and longitude: ' + lng);
    // });

});

async function loadMap() {
    let coords = await request("GET", "../../Controllers/Address/getCoords.php", {});
    coords = JSON.parse(coords).coords;

    // coordinate di Milano
    let lat = coords.latitudine;
    let lng = coords.longitudine;
    let zoom = 15;
    let map = L.map('map').setView([lat, lng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
}