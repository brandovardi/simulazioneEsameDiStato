$(document).ready(async function () {

    let tableData = await request('GET', '../../Controllers/Read/Admin/getReport.php', {});
    tableData = JSON.parse(tableData);

    let reports = tableData.reports;
    for (let i = 0; i < tableData.numBici / 10; i++) {
        let page = `<li class="page-item"><a class="page-link" href="#" onclick="changePagination()">${i + 1}</a></li>`;
        if (i == 0) {
            page = `<li class="page-item active"><a class="page-link" href="#" onclick="changePagination()">${i + 1}</a></li>`;
        }
        $('#pagination').append(page);
    }

    for (let i = 0; i < reports.length; i++) {
        let report = reports[i];
        let reportRow = `
            <tr>
                <td>${i + 1}</td>
                <td>${report.codice}</td>
                <td>${report.kmEffettuati}</td>
                <td>${report.GPS}</td>
                <td>${report.RFID}</td>
                <td title="Per modificare lo stato della manutenzione andare sulla mappa">${(report.manutenzione == "0") ? "false" : "true"}</td>
                <td>${(report.stazione == null) ? await genStazioni(report.codice) : report.stazione}</td>
            </tr>
        `;
        $('#reportTable').append(reportRow);
    }

    // Attach event listener for select change
    $('#reportTable').on('change', 'select', function (e) {
        let code = $(e.target).attr('id');
        popupConfirmChangeStation(code);
    });

});

async function changePagination() {
    // Change the active page
    $('#pagination').find('.active').removeClass('active');
    $(event.target).parent().addClass('active');
    let page = $(event.target).text();

    let tableData = await request('GET', '../../Controllers/Read/Admin/getReport.php', { pagina: page });
    tableData = JSON.parse(tableData);

    let reports = tableData.reports;
    $('#reportTable').html('');
    for (let i = 0; i < reports.length; i++) {
        let report = reports[i];
        let reportRow = `
            <tr>
                <td>${i + 1}</td>
                <td>${report.codice}</td>
                <td>${report.kmEffettuati}</td>
                <td>${report.GPS}</td>
                <td>${report.RFID}</td>
                <td title="Per modificare lo stato della manutenzione andare sulla mappa">${(report.manutenzione == "0") ? "false" : "true"}</td>
                <td>${(report.stazione == null) ? await genStazioni(report.codice) : report.stazione}</td>
            </tr>
        `;
        $('#reportTable').append(reportRow);
    }
}

async function genStazioni(code) {
    let select = $("<select></select>");
    select.addClass("form-control");
    select.attr("id", code);

    let jsonStations = JSON.parse(await request("GET", "../../Controllers/Read/Address/getStationAddress.php", {})).coords;

    select.append(`<option value="null">Nessuna</option>`);
    jsonStations.forEach(station => {
        select.append(`<option value="${station.codice}">${station.codice} - ${station.comune}</option>`);
    });

    return select.prop('outerHTML');
}

function popupConfirmChangeStation(code) {
    let confirmModal = `
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmChangeStation"
    aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmChangeStation">Conferma azione</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Vuoi davvero assegnare la stazione?<br>
                    (Se decici di cambiare la stazione, per poterla successivamente modificare dovrai farlo dalla mappa delle stazioni)
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Conferma</button>
                </div>
            </div>
        </div>
    </div>
    `;

    $('#confirmModal').remove();

    $("body").append(confirmModal);
    $('#confirmModal').modal('show');

    // Add action to the confirm button if needed
    $('#confirmAction').on('click', async function () {
        // Handle the confirm action here
        $('#confirmModal').modal('hide');
        let station = $('#reportTable').find('select#' + code).val();
        let response = await request('POST', '../../Controllers/Update/assignStation.php', { bikeCode: code, stationCode: station });
        response = JSON.parse(response);

        alert(response.message);

        location.reload();
    });

    // se esce dal popup senza confermare, rimetto la select come prima
    $('#confirmModal').on('hidden.bs.modal', function () {
        $('#reportTable').find('select#' + code).val('null');
        alert("Operazione annullata");
    });
}
