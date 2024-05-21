$(document).ready(async function () {
    let coords = await request("GET", "../../Controllers/Get/Address/getCoords.php", {});
    let jsonCoords = JSON.parse(coords);
    coords = jsonCoords.coords;

    // coordinate di Milano
    let lat = coords.latitudine;
    let lng = coords.longitudine;
    let zoom = 15;
    let map = L.map('map').setView([lat, lng], zoom);

    L.marker([lat, lng]).addTo(map);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);
    

});