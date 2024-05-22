$(document).ready(async function () {
    let coords = await request("GET", "../../Controllers/Get/Address/getCoords.php", {});
    let jsonCoords = JSON.parse(coords);
    coords = jsonCoords.coords;

    // coordinate di Milano
    let lat = coords.latitudine;
    let lng = coords.longitudine;
    let zoom = 15;
    let map = L.map('map').setView([lat, lng], zoom);

    let myPosition = L.marker([lat, lng]).addTo(map);
    myPosition.bindPopup("<b>La tua posizione</b>").openPopup();
    myPosition.on('click', function (e) {
        map.setView(e.latlng, 12);
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    let response = await request("GET", "../../Controllers/Get/Address/getStationAddress.php", {});
    let jsonStation = JSON.parse(response);
    let stationCoords = jsonStation.coords;

    for (let i = 0; i < stationCoords.length; i++) {
        let lat = stationCoords[i].latitudine;
        let lng = stationCoords[i].longitudine;
        let marker = L.marker([lat, lng]).addTo(map);
        let popUpText = `
            <h6 style='color:green;'><b>Stazione di ${stationCoords[i].comune}</b></h6>
            <p style='color:blue;'>Posti Disponibili: ${stationCoords[i].numero_slot - stationCoords[i].numBici}</p>
        `;

        marker.bindPopup(popUpText);
        marker.on('click', async function (e) {
            map.setView(e.latlng, 12);
        });
    }

});