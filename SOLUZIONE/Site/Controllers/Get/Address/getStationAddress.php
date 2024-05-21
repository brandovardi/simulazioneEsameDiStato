<?php
include_once("../../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    echo json_encode(array("status" => "errore", "message" => "Utente non autenticato"));
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
    exit;
}

$select = "SELECT s.codice, s.numero_slot, i.*, COUNT(b.ID) AS numBici
FROM stazione AS s
JOIN indirizzo AS i ON s.id_indirizzo = i.ID
LEFT JOIN bicicletta AS b ON s.ID = b.id_stazione
GROUP BY s.codice";
$result = $conn->query($select);

if ($result->num_rows <= 0) {
    echo json_encode(array("status" => "errore", "message" => "Stazione non trovata"));
    exit;
}

$coords = array();
while ($row = $result->fetch_assoc()) {
    $coords[] = $row;
}
echo json_encode(array("status" => "success", "coords" => $coords));