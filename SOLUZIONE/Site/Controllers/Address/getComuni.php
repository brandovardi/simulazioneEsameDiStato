<?php
include_once("../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_GET['sigla_provincia'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_comuni);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

$select = "SELECT denominazione_ita_altra FROM gi_comuni WHERE sigla_provincia = ? ORDER BY denominazione_ita_altra ASC";
$stmt = $conn->prepare($select);
$stmt->bind_param("s", $_GET['sigla_provincia']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows <= 0) {
    echo json_encode(array("status" => "error", "message" => "Nessuna provincia trovata nel database"));
    exit;
}

$comuni = array();
while ($row = $result->fetch_assoc()) {
    $comuni[] = $row['denominazione_ita_altra'];
}

echo json_encode(array("status" => "ok", "comuni" => $comuni));