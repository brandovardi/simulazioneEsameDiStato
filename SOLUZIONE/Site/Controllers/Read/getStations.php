<?php

include_once("../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

if (isset($_GET["latitudine"]) && isset($_GET["longitudine"]))
{
    $select = "SELECT * FROM stazione AS s JOIN indirizzo AS i ON i.ID = s.id_indirizzo WHERE i.latitudine = ? AND i.longitudine = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("dd", $_GET["latitudine"], $_GET["longitudine"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0)
    {
        $station = $result->fetch_assoc();
        echo json_encode(array("status" => "success", "station" => $station));
        exit;
    }

}
else
{
    $select = "SELECT * FROM stazione";
    $result = $conn->query($select);

    $stations = array();
    while (($row = $result->fetch_assoc()) != null)
    {
        $stations[] = $row;
    }

    echo json_encode(array("status" => "success", "stations" => $stations));
    exit;
}