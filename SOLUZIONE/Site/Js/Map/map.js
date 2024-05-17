$(document).ready(async function () {
    let coords = await request("GET", "../../Controllers/Address/getCoords.php", {});
    let jsonCoords = JSON.parse(coords);
    coords = jsonCoords.coords;

    let zm = 15;
    if (jsonCoords.is_admin != undefined && jsonCoords.is_admin == 1) {
        zm = 5;
    }

    // coordinate di Milano
    let lat = coords.latitudine;
    let lng = coords.longitudine;
    let zoom = zm;
    let map = L.map('map').setView([lat, lng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    let response = await request("GET", "../../Controllers/Address/getStationAddress.php", {});
    console.log(response);
    let jsonStation = JSON.parse(response);
    let stationCoords = jsonStation.coords;

    for (let i = 0; i < stationCoords.length; i++) {
        let lat = stationCoords[i].latitudine;
        let lng = stationCoords[i].longitudine;
        let marker = L.marker([lat, lng]).addTo(map)
        // .bindPopup("Stazione di " + stationCoords[i].comune).openPopup();
        let popUpText = `
            <h6 style='color:green;'><b>Stazione di ${stationCoords[i].comune}</b></h6>
            <p style='color:grey;'>Posti Disponibili: ${stationCoords[i].numero_slot - stationCoords[i].numBici}</p>
        `;
        marker.bindPopup(popUpText);
    }

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