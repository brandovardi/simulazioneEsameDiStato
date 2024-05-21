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
        `;

        marker.bindPopup(popUpText);
        marker.on('click', async function (e) {
            map.setView(e.latlng, 12);
        });

        genPopUpModifyStation(code);
        $('#editStation_' + code).on('show.bs.modal', async function (e) {
            let codice = $(this).attr('id').split('_')[1];

            insertDataIntoPopup(codice);
        });

        $('#editStation_' + code).on('hidden.bs.modal', function () {

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

        let response = await request("POST", "../../Controllers/Put/addStation.php", { regione: regione, provincia: provincia, comune: comune, cap: cap, via: via, numeroCivico: numeroCivico, numero_slot: numero_slot });
        response = JSON.parse(response);
        if (response.status == "success") {
            alert("Stazione inserita con successo");
            location.reload();
        } else {
            alert("Errore nell'inserimento della stazione");
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
                    <h5 class="modal-title" id="newStationLabel">Aggiungi Nuova Stazione</h5>
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
                    (Le bici collegate alla stazione dovranno poi essere spostate manualmente)
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

async function insertDataIntoPopup(codice) {

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
