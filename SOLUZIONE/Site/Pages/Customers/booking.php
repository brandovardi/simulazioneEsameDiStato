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
    <title>Prenotazione</title>
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

</body>

</html>