$(document).ready(function() {
    $("#loginForm").submit(function(e) {
        e.preventDefault();

        let username = $("#username").val();
        let password = $("#password").val();
        let numeroTessera = $("#numeroTessera").val();
        if (username == "" || password == "" || numeroTessera == "") {
            $("#error").html("Compila tutti i campi");
            return false;
        }

        $.ajax({
            type: "POST",
            url: $("#loginForm").attr("action"),
            data: {
                username: username,
                password: password,
                numeroTessera: numeroTessera
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