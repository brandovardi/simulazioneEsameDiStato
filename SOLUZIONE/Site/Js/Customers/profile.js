$(document).ready(async function () {
    await loadRegioni();
    await loadProvince();
    await loadComuni();

    $("#regione").change(async function () {
        denominazione_regione = ($("#regione").val() === null) ? "Abruzzo" : $("#regione").val();

        let province = await request("GET", "../../Controllers/Read/Address/getProvince.php", { denominazione_regione: denominazione_regione });
        province = JSON.parse(province).province;

        let selectProv = $("#provincia");
        selectProv.html("");

        province.forEach(provincia => {
            selectProv.append(`<option value="${provincia.split("-")[0]}">${provincia}</option>`);
        });

        await $("#provincia").trigger("change");
    });

    $("#provincia").change(async function () {
        let sigla_provincia = $("#provincia").val().split("-")[0];

        let comuni = await request("GET", "../../Controllers/Read/Address/getComuni.php", { sigla_provincia: sigla_provincia });
        comuni = JSON.parse(comuni).comuni;

        let selectComune = $("#comune");
        selectComune.html("");

        comuni.forEach(comune => {
            selectComune.append(`<option value="${comune}">${comune}</option>`);
        });

        await $("#comune").trigger("change");
    });

    $("#comune").change(async function () {
        let denominazione_ita_altra = $("#comune").val();

        let cap = await request("GET", "../../Controllers/Read/Address/getCap.php", { denominazione_ita_altra: denominazione_ita_altra });
        cap = JSON.parse(cap).cap[0];

        $("#cap").val(cap);
    });

    await start();

    $('#modifica').click(async function () {
        $("#nome").attr("readonly", !$("#nome").attr("readonly"));
        $("#cognome").attr("readonly", !$("#cognome").attr("readonly"));
        $("#conferma").attr("disabled", !$("#conferma").attr("disabled"));

        $("#regione").attr("disabled", !$("#regione").attr("disabled"));
        $("#provincia").attr("disabled", !$("#provincia").attr("disabled"));
        $("#comune").attr("disabled", !$("#comune").attr("disabled"));

        $("#via").attr("readonly", !$("#via").attr("readonly"));
        $("#numeroCivico").attr("readonly", !$("#numeroCivico").attr("readonly"));

        if ($("#modifica").html() == "Modifica")
            $(this).html("Annulla");
        else{
            $(this).html("Modifica");
            await start();
        }
    });

    $('#conferma').click(async function () {
        let nome = $("#nome").val();
        let cognome = $("#cognome").val();
        let regione = $("#regione").val();
        let provincia = $("#provincia").val();
        let comune = $("#comune").val();
        let cap = $("#cap").val();
        let via = $("#via").val();
        let numeroCivico = $("#numeroCivico").val();
        if (nome == "" || cognome == "" || regione == "" ||
        provincia == "" || comune == "" || cap == "" || via == "" || numeroCivico == ""
        ) {
            alert("Compilare tutti i campi");
            return;
        }
        let data = {
            nome: nome,
            cognome: cognome,
            regione: regione,
            provincia: provincia,
            comune: comune,
            cap: cap,
            via: via,
            numeroCivico: numeroCivico
        };

        let response = await request("POST", "../../Controllers/Update/Customers/updateProfile.php", data);
        response = JSON.parse(response);

        if (response.status == "success") {
            $("#username").attr("readonly", !$("#username").attr("readonly"));
            $("#nome").attr("readonly", !$("#nome").attr("readonly"));
            $("#cognome").attr("readonly", !$("#cognome").attr("readonly"));

            $("#conferma").attr("disabled", !$("#conferma").attr("disabled"));
            alert("Profilo modificato con successo");
        }
        else {
            alert(response.message);
        }
    });
});

async function start() {

    let response = await request("POST", "../../Controllers/Read/Customers/getProfile.php", {});
    response = JSON.parse(response);

    if (response.status == "success") {
        let user = response.user;

        $("#nome").val(user.nome);
        $("#cognome").val(user.cognome);
        $("#numeroCartaCredito").val(user.numeroCartaCredito);
        $("#email").val(user.email);

        $("#regione").val(user.regione);
        await $("#regione").trigger("change");

        await loadProvince($("#regione").val());
        $("#provincia").val(user.provincia);

        await loadComuni($("#provincia").val());
        $("#comune").val(user.comune);
        await $("#comune").trigger("change");

        $("#via").val(user.via);
        $("#numeroCivico").val(user.numeroCivico);
    }
    else {
        alert(response.message);
    }
}

async function loadRegioni() {
    let regioni = await request("GET", "../../Controllers/Read/Address/getRegioni.php", {});
    regioni = JSON.parse(regioni).regioni;
    let selectReg = $("#regione");
    regioni.forEach(regione => {
        selectReg.append(`<option value="${regione}">${regione}</option>`);
    });
}

async function loadProvince(regione = "Abruzzo") {
    let province = await request("GET", "../../Controllers/Read/Address/getProvince.php", { denominazione_regione: regione });
    province = JSON.parse(province).province;
    let selectProv = $("#provincia");
    province.forEach(provincia => {
        selectProv.append(`<option value="${provincia.split("-")[0]}">${provincia}</option>`);
    });
}

async function loadComuni(provincia = "CH") {
    let comuni = await request("GET", "../../Controllers/Read/Address/getComuni.php", { sigla_provincia: provincia });
    comuni = JSON.parse(comuni).comuni;
    let selectComune = $("#comune");
    comuni.forEach(comune => {
        selectComune.append(`<option value="${comune}">${comune}</option>`);
    });
}