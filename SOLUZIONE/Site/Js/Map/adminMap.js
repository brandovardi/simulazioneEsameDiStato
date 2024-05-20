$(document).ready(async function () {
    let coords = await request("GET", "../../Controllers/Address/getCoords.php", {});
    let jsonCoords = JSON.parse(coords);
    coords = jsonCoords.coords;

    // coordinate di Milano
    let lat = coords.latitudine;
    let lng = coords.longitudine;
    let zoom = 5;
    let map = L.map('map').setView([lat, lng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    let response = await request("GET", "../../Controllers/Address/getStationAddress.php", {});
    let jsonStation = JSON.parse(response);
    let stationCoords = jsonStation.coords;

    for (let i = 0; i < stationCoords.length; i++) {
        let lat = stationCoords[i].latitudine;
        let lng = stationCoords[i].longitudine;
        let marker = L.marker([lat, lng]).addTo(map);
        let popUpText = `
            <h6 style='color:green;'><b>Stazione di ${stationCoords[i].comune}</b></h6>
            <p style='color:blue;'>Posti Disponibili: ${stationCoords[i].numero_slot - stationCoords[i].numBici}</p>
            <button class='btn btn-primary' onclick='editStation(${stationCoords[i].codice})'>Modifica</button>
        `;
        marker.bindPopup(popUpText);
        marker.on('click', async function (e) {
            map.setView(e.latlng, 15);
        });

        let select = $("#selectStation");
        select.append(`<option value="${lat};${lng};${stationCoords[i].codice}">${stationCoords[i].codice} - ${stationCoords[i].comune}</option>`);

        select.on("change", function () {
            let coords = $(this).val().split(";");
            map.setView([coords[0], coords[1]], 15);
            let codice = coords[2];
            editStation(codice);
        });

    }

});

function editStation(codice) {
    if (codice == null) {
        return;
    }

    let popup = generatePopUp();
    let form = $(popup);

    $("body").append(form);

}

function generatePopUp() {
    let popUp = `
        <from id="popup">
            <div class="form-group">
                <label for="indirizzo">Indirizzo</label>
                <input type="text" class="form-control" id="indirizzo" placeholder="Indirizzo" disabled>
            </div>
            <div class="form-group">
                <label for="codice_slot">Codice</label>
                <input type="number" class="form-control" id="codice_slot" placeholder="Codice slot" disabled>
            </div>
            <div class="form-group">
                <label for="numero_slot">Numero Slot</label>
                <input type="number" class="form-control" id="numero_slot" placeholder="Numero Slot" disabled>
            </div>
            <div class="form-group">
                <label for="numBici">Numero Bici</label>
                <input type="number" class="form-control" id="numBici" placeholder="Numero Bici" disabled>
            </div>

            <button type="submit" class="btn btn-primary">Salva</button>
        </form>
    `;
    return popUp;
}
