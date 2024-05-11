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
    <title>Users Home</title>
    <link rel="stylesheet" href="../../Js/Map/leaflet/leaflet.css"/>
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="../../Js/Map/leaflet/leaflet.js" defer></script>
    <script src="../../Js/jquery-3.7.1.min.js"defer></script>
    <script src="../../Js/Map/map.js" defer></script>
    <style>
        #map {
            height: 500px;
        }
    </style>
</head>

<body>

    <div id="map"></div>

</body>

</html>