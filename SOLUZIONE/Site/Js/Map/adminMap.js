$(document).ready(async function () {
    let coords = await request("GET", "../../Controllers/Get/Address/getCoords.php", {});
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

    let response = await request("GET", "../../Controllers/Get/Address/getStationAddress.php", {});
    let jsonStation = JSON.parse(response);
    let stationCoords = jsonStation.coords;

    for (let i = 0; i < stationCoords.length; i++) {
        let lat = stationCoords[i].latitudine;
        let lng = stationCoords[i].longitudine;
        let marker = L.marker([lat, lng]).addTo(map);
        let code = stationCoords[i].codice;
        let popUpText = `
            <h6 style='color:green;'><b>Stazione di ${stationCoords[i].comune}</b></h6>
            <p style='color:blue;'>Posti Disponibili: ${stationCoords[i].numero_slot - stationCoords[i].numBici}</p>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editStation_${code}"">
                Modifica
            </button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editBikes_${code}"">
                Visualizza Biciclette
            </button>
        `;

        marker.bindPopup(popUpText);
        marker.on('click', async function (e) {
            map.setView(e.latlng, 12);
            $('#selectStation').val(`${e.latlng.lat};${e.latlng.lng};${code}`);
        });

        genPopUpModifyStation(code);
        $('#editStation_' + code).on('show.bs.modal', async function (e) {
            let codice = $(this).attr('id').split('_')[1];

            insertDataIntoPopup(codice, "station");
        });
        genPopUpModifyBike(code);
        $('#editBikes_' + code).on('show.bs.modal', async function (e) {
            let codice = $(this).attr('id').split('_')[1];

            insertDataIntoPopup(codice, "bikes");
        });

        let select = $("#selectStation");
        select.append(`<option value="${lat};${lng};${code}">${code} - ${stationCoords[i].comune}</option>`);

        $('#saveChangesBtn_' + code).on('click', async function () {
            let codice = code;
            let regione = $('#regione_' + code).val();
            let provincia = $('#provincia_' + code).val();
            let comune = $('#comune_' + code).val();
            let cap = $('#cap_' + code).val();
            let via = $('#via_' + code).val();
            let numeroCivico = $('#numeroCivico_' + code).val();
            let numeroSlot = $('#numeroSlot_' + code).val();

            let response = await request("POST", "../../Controllers/Update/updateStation.php", { codice: codice, regione: regione, provincia: provincia, comune: comune, cap: cap, via: via, numeroCivico: numeroCivico, numero_slot: numeroSlot });
            response = JSON.parse(response);
            if (response.status == "success") {
                alert("Stazione modificata con successo");
                location.reload();
            } else {
                alert("Errore nella modifica della stazione");
            }
        });
    }

    $("#selectStation").on("change", async function () {
        let coords = $(this).val().split(";");
        map.setView([coords[0], coords[1]], 12);
        let codice = coords[2];
        $('#editStation_' + codice).modal('show');
    });

    popupConfirmDeleteStation();

    $('#confirmAction').on('click', async function () {
        let codice = "";
        try {
            codice = $("#selectStation").val().split(";")[2];
        }
        catch (e) {
            codice = $("#codiceStazione").val();
        }

        let response = await request("POST", "../../Controllers/Delete/deleteStation.php", { codice: codice });
        response = JSON.parse(response);
        if (response.status == "success") {
            alert("Stazione eliminata con successo");
            location.reload();
        } else {
            alert("Errore nell'eliminazione della stazione");
        }
    });

    genPopUpNewStation();
    $("#regione").change(async function () {
        denominazione_regione = ($("#regione").val() === null) ? "Abruzzo" : $("#regione").val();

        let province = await request("GET", "../../Controllers/Get/Address/getProvince.php", { denominazione_regione: denominazione_regione });
        province = JSON.parse(province).province;

        let selectProv = $("#provincia");
        selectProv.html("");

        province.forEach(provincia => {
            selectProv.append(`<option value="${provincia.split("-")[0]}">${provincia}</option>`);
        });

        $("#provincia").trigger("change");
    });

    $("#provincia").change(async function () {
        let sigla_provincia = $("#provincia").val().split("-")[0];

        let comuni = await request("GET", "../../Controllers/Get/Address/getComuni.php", { sigla_provincia: sigla_provincia });
        comuni = JSON.parse(comuni).comuni;

        let selectComune = $("#comune");
        selectComune.html("");

        comuni.forEach(comune => {
            selectComune.append(`<option value="${comune}">${comune}</option>`);
        });

        $("#comune").trigger("change");
    });

    $("#comune").change(async function () {
        let denominazione_ita_altra = $("#comune").val();

        let cap = await request("GET", "../../Controllers/Get/Address/getCap.php", { denominazione_ita_altra: denominazione_ita_altra });
        cap = JSON.parse(cap).cap;

        $("#cap").val(cap[0]);
    });

    loadRegioni();
    $("#regione").trigger("change");

    $("#btnAddStation").on("click", async function () {
        let regione = $("#regione").val();
        let provincia = $("#provincia").val();
        let comune = $("#comune").val();
        let cap = $("#cap").val();
        let via = $("#via").val();
        let numeroCivico = $("#numeroCivico").val();
        let numero_slot = $("#numeroSlot").val();

        let response = await request("POST", "../../Controllers/Create/addStation.php", { regione: regione, provincia: provincia, comune: comune, cap: cap, via: via, numeroCivico: numeroCivico, numero_slot: numero_slot });
        response = JSON.parse(response);
        if (response.status == "success") {
            alert("Stazione inserita con successo");
            location.reload();
        } else {
            alert("Errore nell'inserimento della stazione");
        }
    });

    genPopUpNewBike();
    $('#newBike').on('show.bs.modal', async function (e) {
        let response = await request("GET", "../../Controllers/Get/Address/getStationAddress.php", {});
        let jsonCoords = JSON.parse(response).coords;

        let select = $("#stazione");
        select.html("");

        select.append(`<option value="None">Nessuna</option>`);
        jsonCoords.forEach(coords => {
            if (coords.numBici < coords.numero_slot)
                select.append(`<option value="${coords.codice}">${coords.codice} - ${coords.comune}</option>`);
        });
    });

    $("#btnAddBike").on("click", async function () {
        let codiceStazione = $("#stazione").val();
        let manutenzione = $("#manutenzione").val();
        let gps = $("#gps").val();
        let rfid = $("#rfid").val();

        let response = await request("POST", "../../Controllers/Create/addBike.php", { codiceStazione: codiceStazione, manutenzione: manutenzione, gps: gps, rfid: rfid });
        response = JSON.parse(response);
        if (response.status == "success") {
            alert("Bicicletta inserita con successo");
            location.reload();
        } else {
            alert("Errore nell'inserimento della bicicletta");
        }
    });

});

async function loadRegioni() {
    let regioni = await request("GET", "../../Controllers/Get/Address/getRegioni.php", {});
    regioni = JSON.parse(regioni).regioni;
    let selectReg = $("#regione");
    regioni.forEach(regione => {
        selectReg.append(`<option value="${regione}">${regione}</option>`);
    });
}

function genPopUpNewStation() {
    let PopUp = `
    <!-- Modal -->
    <div class="modal fade" id="newStation" tabindex="-1" role="dialog" aria-labelledby="newStationLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newStationLabel">Aggiungi Una Nuova Stazione</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="newStationForm">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="regione">Regione:</label>
                                <select name="regione" class="form-control" id="regione"></select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="provincia">Provincia:</label>
                                <select name="provincia" class="form-control" id="provincia"></select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="comune">Comune:</label>
                                <select name="comune" class="form-control" id="comune"></select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cap">CAP:</label>
                                <input type="number" class="form-control" id="cap" required disabled />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="via">Via/Viale:</label>
                                <input type="text" class="form-control" id="via" required />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="numeroCivico">Numero Civico:</label>
                                <input type="number" class="form-control" id="numeroCivico" required />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="numeroSlot">Numero Slot Totali:</label>
                                <input type="number" class="form-control" id="numeroSlot" required />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-success" id="btnAddStation">Aggiungi</button>
                </div>
            </div>
        </div>
    </div>
    `;
    $("body").append(PopUp);
}

function genPopUpNewBike() {
    let PopUp = `
    <!-- Modal -->
    <div class="modal fade" id="newBike" tabindex="-1" role="dialog" aria-labelledby="newBikeLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newBikeLabel">Aggiungi Una Nuova Bicicletta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="newBikeForm">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="stazione">Stazione:</label>
                                <select name="stazione" class="form-control" id="stazione"></select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="manutenzione">Manutenzione:</label>
                                <select name="manutenzione" class="form-control" id="manutenzione">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="gps">GPS:</label>
                                <input type="number" class="form-control" id="gps" required />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rfid">RFID:</label>
                                <input type="number" class="form-control" id="rfid" required />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-success" id="btnAddBike">Aggiungi</button>
                </div>
            </div>
        </div>
    </div>
    `;
    $("body").append(PopUp);
}

function genPopUpModifyStation(id) {
    let PopUp = `
    <!-- Modal -->
    <div class="modal fade" id="editStation_${id}" tabindex="-1" role="dialog" aria-labelledby="editStationLabel_${id}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStationLabel_${id}">Modifica Dati Stazione</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editStationForm">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="codice_${id}">Codice Stazione:</label>
                                <input type="number" class="form-control" id="codice_${id}" disabled />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="regione_${id}">Regione:</label>
                                <input type="text" class="form-control" id="regione_${id}" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="provincia_${id}">Provincia:</label>
                                <input type="text" class="form-control" id="provincia_${id}" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="comune_${id}">Comune:</label>
                                <input type="text" class="form-control" id="comune_${id}" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cap_${id}">CAP:</label>
                                <input type="number" class="form-control" id="cap_${id}" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="via_${id}">Via/Viale:</label>
                                <input type="text" class="form-control" id="via_${id}" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="numeroCivico_${id}">Numero Civico:</label>
                                <input type="number" class="form-control" id="numeroCivico_${id}" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="numeroSlot_${id}">Numero Slot Totali:</label>
                                <input type="number" class="form-control" id="numeroSlot_${id}" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-success" id="saveChangesBtn_${id}">Salva Modifiche</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmModal">
                        Elimina Stazione
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;
    $("body").append(PopUp);
}

function genPopUpModifyBike(id) {
    let PopUp = `
    <!-- Modal -->
    <div class="modal fade" id="editBikes_${id}" tabindex="-1" role="dialog" aria-labelledby="editBikesLabel_${id}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBikesLabel_${id}">Modifica Biciclette</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editBikeForm">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Codice</th>
                                    <th scope="col">KM Effettuati</th>
                                    <th scope="col">Manutenzione</th>
                                    <th scope="col">-</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    `;
    $("body").append(PopUp);
}

function genPopUpModifyBikeSingle(id) {
    let PopUp = `
    <!-- Modal -->
    <div class="modal fade" id="editBike_${id}" tabindex="-1" role="dialog" aria-labelledby="editBikeLabel_${id}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBikeLabel_${id}">Aggiungi Una Nuova Bicicletta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="newStationForm">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="codice_${id}">Codice Bicicletta:</label>
                                <input type="text" class="form-control" id="codice_${id}" disabled required />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="stazione_${id}">Stazione:</label>
                                <select name="stazione_${id}" class="form-control" id="stazione_${id}">
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="manutenzione_${id}">Manutenzione:</label>
                                <select name="manutenzione_${id}" class="form-control" id="manutenzione_${id}">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="gps_${id}">GPS:</label>
                                <input type="text" class="form-control" id="gps_${id}" required />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rfid_${id}">RFID:</label>
                                <input type="text" class="form-control" id="rfid_${id}" required />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-success" id="btnEditBike_${id}">Salva Modifiche</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteBike_${id}">
                        Elimina Bicicletta
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;
    $("body").append(PopUp);
}

function popupConfirmDeleteStation() {
    let confirmModal = `
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteStation"
    aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteStation">Conferma azione</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Vuoi davvero eliminare la stazione stazione?<br>
                    (Le bici all'interno della stazione dovranno poi essere spostate manualmente)
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Conferma</button>
                </div>
            </div>
        </div>
    </div>
    `;
    $("body").append(confirmModal);
}

async function insertDataIntoPopup(codice, type) {

    if (type == "station") {
        let response = await request("GET", "../../Controllers/Get/Address/getStationAddress.php", {});
        let jsonCoords = JSON.parse(response).coords;

        for (let i = 0; i < jsonCoords.length; i++) {
            if (jsonCoords[i].codice == codice) {
                jsonCoords = jsonCoords[i];
                break;
            }
        }

        $('#codice_' + codice).val(jsonCoords.codice);
        $('#regione_' + codice).val(jsonCoords.regione);
        $('#provincia_' + codice).val(jsonCoords.provincia);
        $('#comune_' + codice).val(jsonCoords.comune);
        $('#cap_' + codice).val(jsonCoords.cap);
        $('#via_' + codice).val(jsonCoords.via);
        $('#numeroCivico_' + codice).val(jsonCoords.numeroCivico);

        $('#codiceStazione_' + codice).val(jsonCoords.codice);
        $('#numeroSlot_' + codice).val(jsonCoords.numero_slot);

        $('body').append(`<input type="hidden" id="codiceStazione" value="${jsonCoords.codice}">`);
    }
    else if (type == "bikes") {
        let response = await request("GET", "../../Controllers/Get/Address/getBikeAddress.php", { codice: codice });
        let jsonCoords = JSON.parse(response).coords;
        console.log(jsonCoords);

        let table = $('#editBikes_' + codice + ' tbody');
        table.html("");

        for (let i = 0; i < jsonCoords.length; i++) {
            table.append(`
            <tr>
                <td>${jsonCoords[i].codice}</td>
                <td>${jsonCoords[i].kmEffettuati}</td>
                <td>${(jsonCoords[i].manutenzione == 1) ? "Si" : "No"}</td>
                <td>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editBike_${jsonCoords[i].codice}">
                        Modifica
                    </button>
                </td>
            </tr>
            `);
            genPopUpModifyBikeSingle(jsonCoords[i].codice);

            $('#editBike_' + jsonCoords[i].codice).on('show.bs.modal', async function (e) {
                let codice = $(this).attr('id').split('_')[1];
                insertDataIntoPopup(codice, "bike");
            });

            $('#btnEditBike_' + jsonCoords[i].codice).on('click', async function () {
                let codice = jsonCoords[i].codice;
                let manutenzione = $('#manutenzione_' + codice).val();
                let codiceStazione = $('#stazione_' + codice).val().split(" ")[0];
                let gps = $('#gps_' + codice).val();
                let rfid = $('#rfid_' + codice).val();

                let response = await request("POST", "../../Controllers/Update/updateBike.php", { codice: codice, manutenzione: manutenzione, gps: gps, rfid: rfid, codiceStazione: codiceStazione });
                response = JSON.parse(response);
                if (response.status == "success") {
                    alert("Bicicletta modificata con successo");
                    location.reload();
                } else {
                    alert("Errore nella modifica della bicicletta");
                }
            });

            $('#deleteBike_' + jsonCoords[i].codice).on('click', async function () {
                let codiceBici = jsonCoords[i].codice;

                let response = await request("POST", "../../Controllers/Delete/deleteBike.php", { codiceBici: codiceBici });
                response = JSON.parse(response);
                if (response.status == "success") {
                    alert("Bicicletta eliminata con successo");
                    location.reload();
                } else {
                    alert("Errore nell'eliminazione della bicicletta");
                }
            });
        }
    }
    else if (type == "bike") {
        let response = await request("GET", "../../Controllers/Get/Address/getBikeAddress.php", { codiceBici: codice });
        let jsonCoords = JSON.parse(response).coords;

        let select = $("#stazione_" + codice);
        select.html("");

        let responseStations = await request("GET", "../../Controllers/Get/Address/getStationAddress.php", {});
        let jsonStations = JSON.parse(responseStations).coords;

        select.append(`<option value="None">Nessuna</option>`);
        jsonStations.forEach(station => {
            select.append(`<option value="${station.codice}">${station.codice} - ${station.comune}</option>`);
        });

        console.log(jsonCoords[0]);
        select.val(jsonCoords[0].codice_stazione);
        $('#manutenzione_' + codice).val(jsonCoords[0].manutenzione);
        $('#gps_' + codice).val(jsonCoords[0].GPS);
        $('#rfid_' + codice).val(jsonCoords[0].RFID);
        $('#codice_' + codice).val(codice);

    }
}
