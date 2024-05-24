<?php
include_once ("../../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || !isset($_SESSION['isLogged']) || !$_SESSION['isLogged'] || !isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
    exit;
}

$conn->autocommit(false);
$conn->begin_transaction();

// controllo che la tessera dell'utente non sia bloccata
$select = "SELECT tesseraBloccata FROM cliente WHERE ID = ?";
$stmt = $conn->prepare($select);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['tesseraBloccata'] == 1) {
        $conn->rollback();
        echo json_encode(array("status" => "error", "message" => "Tessera già bloccata, attendere la rigenerazione di una nuova tessera da parte di un amministratore."));
        exit;
    }
}

// vado a modificare la tessera dell'utente
$update = "UPDATE cliente SET tesseraBloccata = 1 WHERE ID = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("i", $user_id);
$stmt->execute();

if ($stmt->affected_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Errore nella modifica della tessera"));
    $conn->rollback();
    exit;
}

$conn->commit();
echo json_encode(array("status" => "success", "message" => "Tessera bloccata correttamente"));