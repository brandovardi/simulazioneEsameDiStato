<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "simulazione_esame");
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

$select = "SELECT * FROM bicicletta WHERE manutenzione = 0";
$result = $conn->query($select);
if ($result->num_rows > 0) {
    $biciclette = array();
    while ($row = $result->fetch_assoc()) {
        $biciclette[] = $row;
    }
    echo json_encode(array("status" => "success", "biciclette" => $biciclette));
} else {
    echo json_encode(array("status" => "error", "message" => "Nessuna bicicletta disponibile"));
}