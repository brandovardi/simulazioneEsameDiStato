<?php
include_once("../../Controllers/mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION["isLogged"]) || !$_SESSION["isLogged"]) || (!isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_POST['codiceBici'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

$codice = $_POST['codiceBici'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione fallita: " . $conn->connect_error));
    exit;
}

$conn->autocommit(false);
$conn->begin_transaction();

// poi cancello la stazione
$delete = "DELETE FROM bicicletta WHERE codice = ?";
$stmt = $conn->prepare($delete);
$stmt->bind_param("i", $codice);
$stmt->execute();

if ($stmt->affected_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Errore nella cancellazione della bicicletta"));
    exit;
}

$conn->commit();

echo json_encode(array("status" => "success", "message" => "Bicicletta cancellata con successo"));