<?php
include_once("../../Controllers/mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION["isLogged"]) || !$_SESSION["isLogged"]) || (!isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"])) {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_POST['codice'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

$codice = $_POST['codice'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione fallita: " . $conn->connect_error));
    exit;
}

$conn->autocommit(false);
$conn->begin_transaction();

// prima vado a togliere tutte le bici dalla stazione
$update = "UPDATE bicicletta SET id_stazione = NULL WHERE id_stazione = (SELECT ID FROM stazione WHERE codice = ?)";
$stmt = $conn->prepare($update);
$stmt->bind_param("i", $codice);
$stmt->execute();

// vado a settare a null tutte le operazioni relative alla stazione
$update = "UPDATE operazione SET id_stazione = NULL WHERE id_stazione = (SELECT ID FROM stazione WHERE codice = ?)";
$stmt = $conn->prepare($update);
$stmt->bind_param("i", $codice);
$stmt->execute();

// poi cancello la stazione
$delete = "DELETE FROM stazione WHERE codice = ?";
$stmt = $conn->prepare($delete);
$stmt->bind_param("i", $codice);
$stmt->execute();

if ($stmt->affected_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Errore nella cancellazione della stazione"));
    exit;
}

$conn->commit();

echo json_encode(array("status" => "success", "message" => "Stazione cancellata con successo"));