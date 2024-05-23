<?php
include_once("../../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_GET['denominazione_ita_altra'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_comuni);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

$select = "SELECT cap FROM gi_cap WHERE codice_istat = (SELECT codice_istat FROM gi_comuni WHERE denominazione_ita_altra = ?)";
$stmt = $conn->prepare($select);
$stmt->bind_param("s", $_GET['denominazione_ita_altra']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows <= 0) {
    echo json_encode(array("status" => "error", "message" => "Nessuna provincia trovata nel database"));
    exit;
}

$comuni = array();
while ($row = $result->fetch_assoc()) {
    $comuni[] = $row['cap'];
}

echo json_encode(array("status" => "ok", "cap" => $comuni));