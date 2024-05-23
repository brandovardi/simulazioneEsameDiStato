<?php
include_once("../../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php");
    exit;
}

if (!isset($_GET['codice']) && !isset($_GET['codiceBici'])) {
    echo json_encode(array("status" => "errore", "message" => "Parametri mancanti"));
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
    exit;
}

$codice = null;

if (isset($_GET['codiceBici']))
    $codice = $_GET['codiceBici'];
else if (isset($_GET['codice']))
    $codice = $_GET['codice'];

$select = null;
if (isset($_GET['codiceBici']))
{
    $select = "SELECT *, s.codice AS codice_stazione
    FROM bicicletta AS b
    JOIN stazione AS s ON b.id_stazione = s.ID
    WHERE b.codice = ?
    LIMIT 1";
}
else
{
    $select = "SELECT b.*
    FROM bicicletta AS b
    JOIN stazione AS s ON b.id_stazione = s.ID
    WHERE s.codice = ?";
}
$stmt = $conn->prepare($select);
$stmt->bind_param("s", $codice);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows <= 0) {
    echo json_encode(array("status" => "errore", "message" => "Bici non trovate"));
    exit;
}

$coords = array();
while ($row = $result->fetch_assoc()) {
    $coords[] = $row;
}
echo json_encode(array("status" => "success", "coords" => $coords));