<?php
include_once ("../mysqliData/dataDB.php");

if (!isset($_GET['GPS']) || !isset($_GET['latitudine']) || !isset($_GET['longitudine'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

$GPS = $_GET['GPS'];
$latitudine = $_GET['latitudine'];
$longitudine = $_GET['longitudine'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
    exit;
}

$conn->autocommit(FALSE);
$conn->begin_transaction();

$distanzaPercorsa = 0;

$select = "SELECT o.ID, o.kmEffettuati AS opKM, b.id_posizione, b.kmEffettuati AS bKM FROM operazione AS o
JOIN bicicletta AS b ON o.id_bicicletta = b.ID
WHERE o.id_bicicletta = (SELECT ID FROM bicicletta WHERE GPS = ?)
AND o.tipo = 'noleggio'
AND (tariffa = 0 OR tariffa = NULL)";
$stmt = $conn->prepare($select);
$stmt->bind_param("s", $GPS);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Noleggio non trovato"));
    exit;
}
$row = $result->fetch_assoc();
$id_operazione = $row['ID'];
$kmEffettuati = $row['opKM'];
$id_indirizzo_vecchio = $row['id_posizione'];
$kmBici = $row['bKM'];

// vado a prendere le ultime lat e lng salvate
$select = "SELECT latitudine, longitudine FROM indirizzo WHERE ID = ?";
$stmt = $conn->prepare($select);
$stmt->bind_param("i", $id_indirizzo_vecchio);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$latitudine_prec = $row['latitudine'];
$longitudine_prec = $row['longitudine'];

// calcolo la distanza percorsa utilizzando la formula di Haversine (https://en.wikipedia.org/wiki/Haversine_formula)
$R = 6371;
$lat1 = deg2rad($latitudine_prec);
$lat2 = deg2rad((double)$latitudine);
$deltaLat = deg2rad((double)$latitudine - (double)$latitudine_prec);
$deltaLng = deg2rad((double)$longitudine - (double)$longitudine_prec);
$a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($lat1) * cos($lat2) * sin($deltaLng / 2) * sin($deltaLng / 2);
$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
$distanzaPercorsa = $R * $c;
$distanzaBici = $kmBici + $distanzaPercorsa;
$distanzaPercorsa = $kmEffettuati + $distanzaPercorsa;

$insert = "INSERT INTO indirizzo (latitudine, longitudine)
VALUES (?, ?)";
$stmt = $conn->prepare($insert);
$stmt->bind_param("dd", $latitudine, $longitudine);
$stmt->execute();
$id_indirizzo = $conn->insert_id;
$stmt->close();

$update = "UPDATE bicicletta SET id_posizione = ?, kmEffettuati = ? WHERE GPS = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("iis", $id_indirizzo, $distanzaBici, $GPS);
$stmt->execute();
$stmt->close();

$update = "UPDATE operazione SET kmEffettuati = ? WHERE ID = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("ii", $distanzaPercorsa, $id_operazione);
$stmt->execute();

// se la distanza percorsa è maggiore di 0, elimino la posizione precedentemente salvata
if ($kmEffettuati > 0) {
    $delete = "DELETE FROM indirizzo WHERE ID = ?";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("i", $id_indirizzo_vecchio);
    $stmt->execute();
    $stmt->close();
}

$conn->commit();
$conn->close();

echo json_encode(array("status" => "success", "message" => "Posizione aggiornata"));
