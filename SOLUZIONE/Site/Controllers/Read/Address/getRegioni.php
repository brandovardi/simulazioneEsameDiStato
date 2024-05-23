<?php
include_once("../../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php");
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_comuni);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

$select = "SELECT denominazione_regione FROM gi_regioni ORDER BY denominazione_regione ASC";
$result = $conn->query($select);

if ($result->num_rows <= 0) {
    echo json_encode(array("status" => "error", "message" => "Nessuna regione trovata nel database"));
    exit;
}

$regioni = array();
while ($row = $result->fetch_assoc()) {
    $regioni[] = $row['denominazione_regione'];
}

echo json_encode(array("status" => "ok", "regioni" => $regioni));