<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) {
    header("Location: ../Admin/home.php");
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
    <script src="../../Js/Map/map.js" defer></script>
    <script src="../../Js/template.js"></script>
    <style>
        #map {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            margin-top: 5%;
            height: 500px;
            width: 30%;
            border-radius: 5%;
            border: 2px solid gray;
        }
    </style>
</head>

<body>
    <nav>
        <script>
            generateNavBar();
        </script>
    </nav>

    <div id="map"></div>
    
</body>

</html>