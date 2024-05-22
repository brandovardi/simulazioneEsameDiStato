<?php
include_once("../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION["isLogged"]) || !$_SESSION["isLogged"]) || (!isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"])) {
    header("Location: ../login.php");
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

$select = "SELECT b.*, CONCAT(s.codice, ' - ', i.comune) AS stazione
FROM bicicletta AS b
LEFT JOIN stazione AS s ON b.id_stazione = s.ID
LEFT JOIN indirizzo AS i ON s.id_indirizzo = i.ID
ORDER BY b.codice ASC
LIMIT " . (($pagina - 1) * 10) . ", 10;";
$result = $conn->query($select);

if ($result->num_rows <= 0) {
    echo json_encode(array("status" => "error", "message" => "Bici non trovate"));
    exit;
}

$coords = array();
while ($row = $result->fetch_assoc()) {
    $coords[] = $row;
}

$select = "SELECT COUNT(*) AS numBici FROM bicicletta;";
$result = $conn->query($select);
$row = $result->fetch_assoc();
$numBici = $row["numBici"];

echo json_encode(array("status" => "success", "reports" => $coords, "numBici" => $numBici));
