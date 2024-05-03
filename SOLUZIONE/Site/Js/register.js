$(document).ready(function() {
    $("#provincia").keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });

    $("#citta, #nome, #cognome").keyup(function() {
        let valore = $(this).val();
        $(this).val(valore.charAt(0).toUpperCase() + valore.slice(1));
    });

    $("#numeroCartaCredito").keyup(function() {
        let valore = $(this).val();
        $(this).val(valore.replace(/[^\d]/g, "").replace(/(.{4})/g, "$1 "));

        if ($(this).val().length > 19) {
            $(this).val(valore.slice(0, 19));
        }
    });

    $("#email").keyup(function() {
        let valore = $(this).val();
        $(this).val(valore.toLowerCase());
        $(this).val(valore.replace(/[^a-zA-Z0-9@._-]/g, ""));
    });

    function validateEmail(email) {
        let regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        return regex.test(email);
    }

    $("form").submit(function(e) {
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

        $.ajax({
            type: $("form").attr("method"),
            url: $("form").attr("action"),
            data: {
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
            },
            success: function(response) {
                response = JSON.parse(response);
                
                if (response.status == "success") {
                    window.location.href = "./home.php";
                } else {
                    $("#error").html(response.message);
                }
            },
            error: function(response) {
                console.log(response);
                $("#error").html("Errore di connessione al server");
            }
        });

        return false;
    });
    
});
