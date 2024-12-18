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
    <title>Report</title>
    <script src="../../Js/Cdn/Jquery/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <link href="../../Css/Cdn/Bootstrap/bootstrap.min.css" rel="stylesheet">
    <script src="../../Js/Cdn/Bootstrap/bootstrap.min.js"></script>

    <link rel="stylesheet" href="../../Js/Map/leaflet/leaflet.css" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="../../Js/Map/leaflet/leaflet.js"></script>
    <script src="../../Js/request.js"></script>
    <script src="../../Js/template.js"></script>
    <script src="../../Js/Admin/report.js" defer></script>
    <style>
        .bordered-div {
            border: 2px solid black;
            border-radius: 5px;
            padding: 20px;
            margin: 20px;
        }
    </style>
</head>

<body>


    <nav>
        <script>
            generateNavBar('admin');
        </script>
    </nav>

    <h1 align="center">Report</h1>

    <div class="container">
        <div class="bordered-div">
            <h2 class="text-center">Dati biciclette</h2>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Codice</th>
                        <th scope="col">Km Effettuati</th>
                        <th scope="col">GPS</th>
                        <th scope="col">RFID</th>
                        <th scope="col">Manutenzione</th>
                        <th scope="col">Stazione</th>
                    </tr>
                </thead>
                <tbody id="reportTable">
                </tbody>
            </table>
        </div>
    </div>

    <div class="container">
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center" id="pagination">
            </ul>
        </nav>
    </div>

</body>

</html>