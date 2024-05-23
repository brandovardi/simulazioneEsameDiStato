<?php
include_once("../../Controllers/mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION["isLogged"]) || !$_SESSION["isLogged"]) || (!isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"])) {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_POST['codiceStazione']) || !isset($_POST['manutenzione']) || !isset($_POST['gps']) || !isset($_POST['rfid'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

$codiceStazione = $_POST['codiceStazione'];
$manutenzione = $_POST['manutenzione'];
$gps = "GPS".$_POST['gps'];
$rfid = "RFID".$_POST['rfid'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione fallita: " . $conn->connect_error));
    exit;
}

$conn->autocommit(false);
$conn->begin_transaction();

$id_stazione = null;
$id_posizione = null;
if ($codiceStazione != null) {
    // controllo se la stazione esiste
    $check = "SELECT * FROM stazione WHERE codice = ?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("s", $codiceStazione);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo json_encode(array("status" => "error", "message" => "Stazione non trovata"));
        exit;
    }

    $stazione = $result->fetch_assoc();
    $id_stazione = $stazione['ID'];
    $id_posizione = $stazione['id_indirizzo'];
}

// prendo il codice dell'ultima bicicletta
$select = "SELECT codice FROM bicicletta ORDER BY codice DESC LIMIT 1";
$result = $conn->query($select);
$codice = null;
if ($result->num_rows == 0) {
    $codice = "B000001";
} else {
    $codice = str_replace("B", "", $result->fetch_assoc()['codice']) + 1;
    $codice = str_pad((string)$codice, 6, "0", STR_PAD_LEFT);
    $codice = "B" . $codice;
}

// inserisco la bicicletta
$insert = "INSERT INTO bicicletta (codice, id_stazione, manutenzione, GPS, RFID, kmEffettuati, id_posizione) VALUES (?, ?, ?, ?, ?, 0, ?)";
$stmt = $conn->prepare($insert);
$stmt->bind_param("siissi", $codice, $id_stazione, $manutenzione, $gps, $rfid, $id_posizione);
$stmt->execute();

if ($stmt->affected_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Errore nell'inserimento della bicicletta"));
    exit;
}

$conn->commit();

echo json_encode(array("status" => "success", "message" => "Bicicletta inserita con successo"));