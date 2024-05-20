$(document).ready(async function () {
    let select = $("#selectStation");

    let response = await request("GET", "../../Controllers/Address/getStationAddress.php", {});
    let stations = JSON.parse(response).coords;

    stations.forEach(station => {
        select.append(`<option value="${station.latitudine};${station.longitudine}">${station.codice} - ${station.comune}</option>`);
    });

    $("#selectStation").change(async function () {
        let latitudine = $(this).val().split(";")[0];
        let longitudine = $(this).val().split(";")[1];
        
        
    });
});