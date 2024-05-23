<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION["isLogged"]) || !$_SESSION["isLogged"]) || (!isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"])) {
    header("Location: ../../index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <script src="../../Js/Cdn/Jquery/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <link href="../../Css/Cdn/Bootstrap/bootstrap.min.css" rel="stylesheet">
    <script src="../../Js/Cdn/Bootstrap/bootstrap.min.js"></script>

    <link rel="stylesheet" href="../../Js/Map/leaflet/leaflet.css" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="../../Js/Map/leaflet/leaflet.js"></script>
    <script src="../../Js/request.js"></script>
    <script src="../../Js/template.js"></script>
    <script src="../../Js/Admin/map.js" defer></script>
    <style>
        #map {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            height: 500px;
            width: 30%;
            border-radius: 5%;
            border: 2px solid gray;
        }

        .container {
            width: 30%;
        }

        .row {
            justify-content: space-evenly;
        }

        /* For Firefox */
        input[type=number] {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }
    </style>
</head>

<body>

    <nav>
        <script>
            generateNavBar('admin');
        </script>
    </nav>

    <h1 align="center">Mappa stazioni</h1>

    <br>
    <div class="container" style="width: 20%;">
        <div class="row">
            <div class="col">
                <label for="selectStation" class="form-label">Stazione Selezionata:</label>
                <select class="form-select" id="selectStation" aria-label="Default select example">
                    <option selected disabled>Seleziona una stazione</option>
                </select>
            </div>
        </div>
    </div>
    <br>
    <div id="map"></div>
    <br>
    <div class="container">
        <div class="row">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newStation">
                Nuova Stazione
            </button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newBike">
                Nuova Bicicletta
            </button>
        </div>
    </div>
</body>

</html>