<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_GET['codice_regione'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

$conn = new mysqli("localhost", "root", "", "gi_db_comuni");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

$select = "SELECT CONCAT(sigla_provincia, '-(', denominazione_provincia, ')') AS provincia FROM gi_province WHERE codice_regione = ? ORDER BY denominazione_provincia ASC";
$stmt = $conn->prepare($select);
$stmt->bind_param("s", $_GET['codice_regione']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows <= 0) {
    echo json_encode(array("status" => "error", "message" => "Nessuna provincia trovata nel database"));
    exit;
}

$province = array();
while ($row = $result->fetch_assoc()) {
    $province[] = $row['provincia'];
}

echo json_encode(array("status" => "ok", "province" => $province));