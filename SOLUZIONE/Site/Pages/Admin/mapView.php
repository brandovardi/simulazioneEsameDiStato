<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
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
    <script src="../../Js/Map/adminMap.js" defer></script>
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
            display: flex;
            margin: 0;
        }

        #popup {
            position: absolute;
            background-color: white;
            border: 1px solid black;
            padding: 5px;
            border-radius: 5px;
            transform: translate(0, -110%);
            margin-left: 2%;
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

    <!-- modifica le stazioni partendo da una select -->
    <div class="container">
        <div class="row">
            <div class="col">
                <select class="form-select" id="selectStation" aria-label="Default select example">
                    <option selected disabled>Seleziona una stazione</option>
                </select>
            </div>
            <div class="col">
                <button type="button" class="btn btn-primary" id="editStation">Modifica</button>
            </div>
        </div>
    </div>

    <div id="map"></div>


    
</body>

</html>