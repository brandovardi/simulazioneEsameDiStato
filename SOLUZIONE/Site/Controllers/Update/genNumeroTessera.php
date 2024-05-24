<?php
include_once ("../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || !isset($_SESSION['isLogged']) || !$_SESSION['isLogged'] || !isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_POST["username"]) || !isset($_POST["email"])) {
    echo json_encode(array("status" => "error", "message" => "Dati mancanti"));
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

$conn->autocommit(false);
$conn->begin_transaction();

$username = $_POST["username"];
$email = $_POST["email"];

$select = "SELECT * FROM cliente WHERE username = ? AND email = ?";
$stmt = $conn->prepare($select);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Utente non trovato"));
    exit;
}

// vado a prendere l'ultimo valore di tessera inserito e lo incremento di 1
$select = "SELECT numeroTessera FROM cliente ORDER BY numeroTessera DESC LIMIT 1";
$result = $conn->query($select);
$row = $result->fetch_assoc();
$numeroTessera = $row['numeroTessera'] + 1;
$numeroTessera = str_pad($numeroTessera, 7, "0", STR_PAD_LEFT);
$_SESSION['numeroTessera'] = $numeroTessera;

$update = "UPDATE cliente SET numeroTessera = ?, tesseraBloccata = 0 WHERE username = ? AND email = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("sss", $numeroTessera, $username, $email);
$stmt->execute();
$id_ciente = $conn->insert_id;

if ($stmt->affected_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Errore nell'aggiornamento del numero tessera"));
    $conn->rollback();
    exit;
}

$conn->commit();

echo json_encode(array("status" => "success", "message" => "Numero tessera aggiornato correttamente"));