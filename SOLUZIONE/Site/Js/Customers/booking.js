$(document).ready(async function() {

    let biciclette = await request("GET", "../../Controllers/Get/getBikes.php", {});

    biciclette = JSON.parse(biciclette).biciclette;
    console.log(biciclette);

    if (biciclette.status == "error") {
        alert("Errore nel caricamento delle biciclette");
        return;
    }

    let table = `
    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Kilometri effettuati</th>
                <th scope="col">Stato</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
    `;

    for (let bicicletta of biciclette) {
        table += `
        <tr>
            <th scope="row">${bicicletta.ID}</th>
            <td>${bicicletta.kmEffettuati}</td>
            <td>${(bicicletta.id_stazione != null) ? 'Libera' : 'Occupata'}</td>
            <td><a href="./confirmBooking.php?id=${bicicletta.ID}" class="btn btn-primary">Prenota</a></td>
        </tr>
        `;
    }

    table += `
        </tbody>
    </table>
    `;

    $("#table").html(table);

});