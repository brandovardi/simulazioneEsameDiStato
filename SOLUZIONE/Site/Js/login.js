$(document).ready(function () {
    $("#loginForm").submit(async function (e) {
        e.preventDefault();

        let username = $("#username").val();
        let password = calc($("#password").val());

        let numeroTessera = $("#numeroTessera").val();
        if (username == "" || password == "") {
            $("#error").html("Compila tutti i campi");
            return false;
        }

        let data = {
            username: username,
            password: password,
            numeroTessera: numeroTessera
        };
        let response = await request($("#loginForm").attr("method"), $("#loginForm").attr("action"), data);

        response = JSON.parse(response);

        if (response.status == "success") {
            window.location.href = "./Customers/home.php";
        } else {
            $("#error").html(response.message);
        }

        return false;
    });
});
