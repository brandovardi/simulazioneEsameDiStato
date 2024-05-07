$(document).ready(function () {
    loadSelect();

    $("#regione").change(function () {
        let province = getProvince($(this).val());
        let select = $("#provincia");
        select.empty();
        province.forEach(provincia => {
            select.append(`<option value="${provincia.split("-")[0]}">${provincia}</option>`);
        });
    });

    $("#provincia").keyup(function () {
        $(this).val($(this).val().toUpperCase());
    });

    $("#username").keyup(function () {
        $(this).val($(this).val().toLowerCase());
    });

    $("#citta, #nome, #cognome").keyup(function () {
        // mi salvo al posizione del cursore
        let start = this.selectionStart;
        $(this).val($(this).val().toLowerCase());
        let valore = $(this).val();

        if (valore.includes(" ")) {
            let array = valore.split(" ");
            let stringa = "";
            
            array.forEach(parola => {
                stringa += parola.charAt(0).toUpperCase() + parola.slice(1) + " ";
            });

            // rimuovo lo spazio finale soltanto se ha inserito una sola parola
            if (array.length == 1) {
                stringa = stringa.trim();
            }

            $(this).val(stringa);
        } else {
            $(this).val(valore.charAt(0).toUpperCase() + valore.slice(1));
        }

        // riposiziono il cursore
        this.selectionStart = start;
        this.selectionEnd = start;
    });

    $("#numeroCartaCredito").keyup(function () {
        let valore = $(this).val();
        $(this).val(valore.replace(/[^\d]/g, "").replace(/(.{4})/g, "$1-"));

        if ($(this).val().length > 19) {
            $(this).val(valore.slice(0, 19));
        }
    });

    $("#email").keyup(function () {
        let valore = $(this).val();
        $(this).val(valore.toLowerCase());
        $(this).val(valore.replace(/[^a-zA-Z0-9@._-]/g, ""));
    });

    $("form").submit(async function (e) {
        e.preventDefault();

        let nome = $("#nome").val();
        let cognome = $("#cognome").val();
        let username = $("#username").val();
        if (!username.includes("_")) {
            $("#error").html("L'username deve contenere il carattere _");
            return false;
        }
        let password = $("#password").val();
        let email = $("#email").val();
        if (!validateEmail(email)) {
            $("#error").html("Email non valida");
            return false;
        }
        let numeroCartaCredito = $("#numeroCartaCredito").val();

        let regione = $("#regione").val();
        let provincia = $("#provincia").val();
        let citta = $("#citta").val();
        let cap = $("#cap").val();
        let via = $("#via").val();
        let numeroCivico = $("#numeroCivico").val();

        let data = {
            nome: nome,
            cognome: cognome,
            username: username,
            password: password,
            email: email,
            numeroCartaCredito: numeroCartaCredito,
            regione: regione,
            provincia: provincia,
            citta: citta,
            cap: cap,
            via: via,
            numeroCivico: numeroCivico
        };

        let response = await request($("form").attr("method"), "../Controllers/checkRegistration.php", data);

        response = JSON.parse(response);
        let message = response.message;

        if (response.status == "success") {
            let data = {
                email: email,
                subject: "Registrazione avvenuta con successo",
                message: message
            };
            let response = await request($("form").attr("method"), "../Controllers/sendEmail.php", data);
            let array = response.split("<br>");

            response = JSON.parse(array[array.length - 1].trim());

            if (response.status == "success") {
                window.location.href = "./login.php";
            } else {
                $("#error").html("Errore nell'invio dell'email di conferma");
            }

        } else {
            $("#error").html(response.message);
        }

        return false;
    });

});

function validateEmail(email) {
    let regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return regex.test(email);
}

async function loadSelect() {
    let regioni = await request("GET", "../Controllers/Address/getRegioni.php", {});
    regioni = JSON.parse(regioni).regioni;
    let selectReg = $("#regione");
    regioni.forEach(regione => {
        selectReg.append(`<option value="${regione}">${regione}</option>`);
    });

    let province = await request("GET", "../Controllers/Address/getProvince.php", { codice_regione: "01" });
    province = JSON.parse(province).province;
    let selectProv = $("#provincia");
    province.forEach(provincia => {
        selectProv.append(`<option value="${provincia.split("-")[0]}">${provincia}</option>`);
    });

    let comuni = await request("GET", "../Controllers/Address/getComuni.php", { sigla_provincia: "AL" });
    console.log(comuni);
    comuni = JSON.parse(comuni).comuni;
    let selectCitta = $("#citta");
    comuni.forEach(comune => {
        selectCitta.append(`<option value="${comune}">${comune}</option>`);
    });
}