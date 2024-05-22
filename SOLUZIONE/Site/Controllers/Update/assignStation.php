<?php
include_once ("../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged'] || !isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    echo json_encode(array("status" => "errore", "message" => "Utente non autenticato"));
    exit;
}

if (!isset($_POST['bikeCode']) || !isset($_POST['stationCode'])) {
    echo json_encode(array("status" => "errore", "message" => "Parametri mancanti"));
    exit;
}

$codiceBici = $_POST['bikeCode'];
$codiceStazione = $_POST['stationCode'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
    exit;
}

$conn->autocommit(false);
$conn->begin_transaction();

$id_stazione = null;
$id_posizione = null;
if ($codiceStazione != null) {
    // controllo che la stazione esista
    $select = "SELECT * FROM stazione WHERE codice = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("i", $codiceStazione);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_stazione = $row['ID'];
        $id_posizione = $row['id_indirizzo'];
    } else {
        echo json_encode(array("status" => "errore", "message" => "Stazione non trovata"));
        exit;
    }
}

// aggiorno la Bicicletta
$update = "UPDATE bicicletta SET id_stazione = ?, id_posizione = ? WHERE codice = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("iis",  $id_stazione, $id_posizione, $codiceBici);
$stmt->execute();

if ($stmt->affected_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Errore nell'assegnamento della stazione"));
    exit;
}

$conn->commit();

echo json_encode(array("status" => "success", "message" => "Stazione assegnata correttamente"));
