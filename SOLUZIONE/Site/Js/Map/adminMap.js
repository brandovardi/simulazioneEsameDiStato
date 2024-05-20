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

async function editStation(codice) {
    if (codice == null) {
        return;
    }

    let popup = await generatePopUp(codice);
    let form = $(popup);

    $("body").append(form);

}

async function generatePopUp(codice) {
    
    let response = await request("GET", "../../Controllers/Address/getStationAddress.php", {});
    let jsonCoords = JSON.parse(response).coords;

    for (let i = 0; i < jsonCoords.length; i++) {
        if (jsonCoords[i].codice == codice) {
            jsonCoords = jsonCoords[i];
            break;
        }
    }

    let popUp = `
        <from id="popup">
            <div class="form-group">
                <label for="indirizzo">Indirizzo</label>
                <input type="text" class="form-control" id="indirizzo" value="${jsonCoords.regione},${jsonCoords.via} ${jsonCoords.numeroCivico}, ${jsonCoords.comune} (${jsonCoords.provincia}), ${jsonCoords.cap}" disabled />
            </div>
            <div class="form-group">
                <label for="codice_slot">Codice</label>
                <input type="number" class="form-control" id="codice_slot" value="${jsonCoords.codice}" disabled />
            </div>
            <div class="form-group">
                <label for="numero_slot">Numero Slot</label>
                <input type="number" class="form-control" id="numero_slot" value="${jsonCoords.numero_slot}" />
            </div>

            <button type="submit" id="btnSalva" class="btn btn-primary">Salva</button>
            <button type="submit" id="btnMdoficaIndirizzo" class="btn btn-primary">Modifica Indirizzo</button>
            <button type="submit" id="btnEliminaStazione" class="btn btn-danger">Elimina Stazione</button>
        </form>
    `;

    $('#btnSalva').on('click', async function () {
        let numero_slot = $('#numero_slot').val();
        let response = await request("POST", "../../Controllers/Address/updateStation.php", { numero_slot: numero_slot });
        response = JSON.parse(response);
        if (response.status == "success") {
            alert("Stazione modificata con successo");
            location.reload();
        } else {
            alert("Errore nella modifica della stazione");
        }
    });

    $('#btnMdoficaIndirizzo').on('click', async function () {
        let response = await request("GET", "../../Controllers/Address/getAddress.php", {});
        let jsonAddress = JSON.parse(response).address;

        let popUp = `
            <form id="popup">
                <div class="form-group">
                    <label for="codice_slot">Codice</label>
                    <input type="number" class="form-control" id="codice_slot" value="${jsonCoords.codice}" disabled />
                </div>
                <div class="form-group">
                    <label for="regione">Regione</label>
                    <input type="text" class="form-control" id="regione" value="${jsonCoords.regione}" />
                </div>
                <div class="form-group">
                    <label for="via">Via</label>
                    <input type="text" class="form-control" id="via" value="${jsonCoords.via}" />
                </div>
                <div class="form-group">
                    <label for="numeroCivico">Numero Civico</label>
                    <input type="text" class="form-control" id="numeroCivico" value="${jsonCoords.numeroCivico}" />
                </div>
                <div class="form-group">
                    <label for="comune">Comune</label>
                    <input type="text" class="form-control" id="comune" value="${jsonCoords.comune}" />
                </div>
                <div class="form-group">
                    <label for="provincia">Provincia</label>
                    <input type="text" class="form-control" id="provincia" value="${jsonCoords.provincia}" />
                </div>
                <div class="form-group">
                    <label for="cap">CAP</label>
                    <input type="text" class="form-control" id="cap" value="${jsonCoords.cap}" />
                </div>
                <button type="submit" id="btnSalvaIndirizzo" class="btn btn-primary">Salva</button>
            </form>
        `;
        $('#popup').remove();
        $("body").append(popUp);
    });
    
    $('#btnSalvaIndirizzo').on('click', async function () {
        let regione = $('#regione').val();
        let via = $('#via').val();
        let numeroCivico = $('#numeroCivico').val();
        let comune = $('#comune').val();
        let provincia = $('#provincia').val();
        let cap = $('#cap').val();

        let response = await request("POST", "../../Controllers/Address/updateAddress.php", { codice: codice, regione: regione, via: via, numeroCivico: numeroCivico, comune: comune, provincia: provincia, cap: cap });
        response = JSON.parse(response);
        if (response.status == "success") {
            alert("Indirizzo modificato con successo");
            location.reload();
        } else {
            alert("Errore nella modifica dell'indirizzo");
        }
    });

    $('#btnEliminaStazione').on('click', async function () {
        let response = await request("POST", "../../Controllers/Address/deleteStation.php", { codice: codice });
        response = JSON.parse(response);
        if (response.status == "success") {
            alert("Stazione eliminata con successo");
            location.reload();
        } else {
            alert("Errore nell'eliminazione della stazione");
        }
    });

    return popUp;
}
