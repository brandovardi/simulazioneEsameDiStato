<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo</title>
    <script src="../../Js/Cdn/Jquery/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <link href="../../Css/Cdn/Bootstrap/bootstrap.min.css" rel="stylesheet">
    <script src="../../Js/Cdn/Bootstrap/bootstrap.min.js"></script>

    <link rel="stylesheet" href="../../Js/Map/leaflet/leaflet.css" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="../../Js/Map/leaflet/leaflet.js"></script>
    <script src="../../Js/request.js"></script>
    <script src="../../Js/template.js"></script>
    <script src="../../Js/Customers/profile.js" defer></script>
    <style>
        input {
            width: 30%;
            margin: 10px;
        }

        .form-container {
            border: 2px solid black;
            padding: 30px;
            border-radius: 8px;
            background-color: #f8f9fa;
            width: 60%;
        }
    </style>
</head>

<body>
    <nav>
        <script>
            generateNavBar();
        </script>
    </nav>

    <div align="center">
        <div class="form-container" align="center">
            <h1 class="text-center mb-4">Il Tuo Profilo</h1>
            <form id="profileForm">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nome">Nome</label>
                        <input type="text" class="form-control" id="nome" readonly />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="cognome">Cognome</label>
                        <input type="text" class="form-control" id="cognome" readonly />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" readonly />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="regione">Regione</label>
                        <select class="form-control" id="regione" disabled></select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="provincia">Provincia</label>
                        <select class="form-control" id="provincia" disabled></select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="comune">Comune</label>
                        <select class="form-control" id="comune" disabled></select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="cap">CAP</label>
                        <input type="text" class="form-control" id="cap" readonly disabled />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="via">Via/Viale/Piazza</label>
                        <input type="text" class="form-control" id="via" readonly />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="numeroCivico">Numero Civico</label>
                        <input type="text" class="form-control" id="numeroCivico" readonly />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="numeroCartaCredito">Numero Carta Di Credito</label>
                        <input type="text" class="form-control" id="numeroCartaCredito" readonly disabled />
                    </div>
                </div>
                <button type="button" class="btn btn-primary" id="modifica">Modifica</button>
                <button type="button" class="btn btn-success" id="conferma" disabled>Conferma</button>
            </form>
        </div>
    </div>

</body>

</html>