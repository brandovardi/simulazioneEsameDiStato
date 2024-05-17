<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    return json_encode(array("status" => "errore", "message" => "Utente non autenticato"));
}

$conn = new mysqli("localhost", "root", "", "simulazione_esame");
$conn->set_charset("utf8");
if ($conn->connect_error) {
    return json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
}

$ID = $_SESSION['user_id'];
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $latitudine = 43.0271328;
    $longitudine = 12.4570495;
    $array = array("status" => "success", "coords" => array("latitudine" => $latitudine, "longitudine" => $longitudine), "is_admin" => true);
    echo json_encode($array);
}
$select = "SELECT i.latitudine, i.longitudine FROM indirizzo AS i JOIN cliente AS c on i.ID = c.id_indirizzo WHERE c.ID = ?";
$stmt = $conn->prepare($select);
$stmt->bind_param("i", $ID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows <= 0) {
    return json_encode(array("status" => "errore", "message" => "Utente non trovato"));
}

$coords = $result->fetch_assoc();
echo json_encode(array("status" => "success", "coords" => $coords));