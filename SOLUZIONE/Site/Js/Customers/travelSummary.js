$(document).ready(async function () {
    let result = await request("GET", "../../Controllers/Read/Customers/getSummary.php", {});
    let viaggi = JSON.parse(result).result.viaggi;

    let tbody = $("#summaryTable");
    tbody.empty();
    viaggi.forEach((viaggio, index) => {
        let row = $("<tr>");
        row.append($("<td>").text(index + 1));
        row.append($("<td>").text(viaggio.stazionePartenza));
        row.append($("<td>").text(viaggio.stazioneArrivo));
        row.append($("<td>").text(viaggio.bicicletta));
        row.append($("<td>").text(viaggio.kmEffettuati));
        row.append($("<td>").text(viaggio.data));
        row.append($("<td>").text(viaggio.tariffa));
        tbody.append(row);
    });

    for (let i = 0; i < viaggi.length / 10; i++) {
        let page = `<li class="page-item"><a class="page-link" href="#" onclick="changePagination()">${i + 1}</a></li>`;
        if (i == 0) {
            page = `<li class="page-item active"><a class="page-link" href="#" onclick="changePagination()">${i + 1}</a></li>`;
        }
        $('#pagination').append(page);
    }

});

async function changePagination() {
    // Change the active page
    $('#pagination').find('.active').removeClass('active');
    $(event.target).parent().addClass('active');
    let page = $(event.target).text();

    let tableData = await request('GET', '../../Controllers/Read/Customers/getSummary.php', { pagina: page });
    viaggi = JSON.parse(tableData);

    let viaggi = JSON.parse(result).result.viaggi;

    let tbody = $("#summaryTable");
    tbody.empty();
    viaggi.forEach((viaggio, index) => {
        let row = $("<tr>");
        row.append($("<td>").text(index + 1));
        row.append($("<td>").text(viaggio.stazionePartenza));
        row.append($("<td>").text(viaggio.stazioneArrivo));
        row.append($("<td>").text(viaggio.bicicletta));
        row.append($("<td>").text(viaggio.kmEffettuati));
        row.append($("<td>").text(viaggio.data));
        row.append($("<td>").text(viaggio.tariffa));
        tbody.append(row);
    });
}
