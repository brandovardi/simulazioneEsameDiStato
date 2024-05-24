<?php
include_once("../../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION["isLogged"]) || !$_SESSION["isLogged"]) || (!isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"])) {
    header("Location: ../../../index.php");
    exit;
}

$pagina = 1;
if (isset($_GET["pagina"])) {
    $pagina = $_GET["pagina"];
}

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

$select = "SELECT username, email, numeroTessera, tesseraBloccata
FROM cliente
WHERE tesseraBloccata = 1
ORDER BY username ASC
LIMIT " . (($pagina - 1) * 10) . ", 10;";
$result = $conn->query($select);

if ($result->num_rows <= 0) {
    echo json_encode(array("status" => "success", "users" => array(), "numUsers" => "0"));
    exit;
}

$users = array();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$select = "SELECT COUNT(*) AS numUsers FROM cliente WHERE tesseraBloccata = 1;";
$result = $conn->query($select);
$row = $result->fetch_assoc();
$numUsers = $row["numUsers"];

echo json_encode(array("status" => "success", "users" => $users, "numUsers" => $numUsers));