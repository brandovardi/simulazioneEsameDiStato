<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo</title>
    <script src="../../Js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link rel="stylesheet" href="../../Js/Map/leaflet/leaflet.css" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="../../Js/Map/leaflet/leaflet.js"></script>
    <script src="../../Js/request.js"></script>
    <script src="../../Js/Map/map.js"></script>
    <script>
        $(document).ready(async function () {
            console.log("ready!");
            let response = await request("POST", "../../Controllers/Customers/getProfile.php", {});
            response = JSON.parse(response);

            if (response.status == "success") {
                var user = response.user;
                $("#username").val(user.username);
                $("#nome").val(user.nome);
                $("#cognome").val(user.cognome);
                $("#email").val(user.email);
                $("#numeroTessera").val(user.numeroTessera);
            }
            else {
                alert(response.message);
            }
        });
        function showCardNumber() {
            $("#numeroTessera").prop("type", $("#numeroTessera").prop("type") === "password" ? "text" : "password");
            $("#btnShowCardNumber").text($("#btnShowCardNumber").text() === "Mostra numero tessera" ? "Nascondi numero tessera" : "Mostra numero tessera");
        }
    </script>
    <style>
        input {
            width: 30%;
            margin: 10px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="./home.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./profile.php">Profilo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./booking.php">Prenotazione</a>
                </li>
            </ul>
            <button class="btn btn-danger" onclick="window.location.href='../logout.php'">Logout</button>
        </div>
    </nav>

    <h1 align="center">Il tuo profilo</h1>

    <div class="container">
        <div class="row">
            <div class="col">
                <h3>Username</h3>
                <input type="text" id="username" disabled />
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h3>Nome</h3>
                <input type="text" id="nome" disabled />
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h3>Cognome</h3>
                <input type="text" id="cognome" disabled />
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h3>Email</h3>
                <input type="email" id="email" disabled />
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h3>Numero tessera</h3>
                <input type="password" id="numeroTessera" disabled />
                <button id="btnShowCardNumber" onclick="showCardNumber()">Mostra numero tessera</button>
            </div>
        </div>
    </div>

</body>

</html>