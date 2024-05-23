<?php
include_once("../../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

$user_id = $_SESSION['user_id'];

$select = "SELECT * FROM cliente AS c
JOIN indirizzo AS i ON c.id_indirizzo = i.ID
WHERE c.ID = ?";
$stmt = $conn->prepare($select);
$stmt->bind_param("i", $user_id);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user['numeroCartaCredito'] = "**** **** **** " . substr($user['numeroCartaCredito'], -4);
    echo json_encode(array("status" => "success", "user" => $user));
} else {
    echo json_encode(array("status" => "error", "message" => "Utente non trovato"));
}