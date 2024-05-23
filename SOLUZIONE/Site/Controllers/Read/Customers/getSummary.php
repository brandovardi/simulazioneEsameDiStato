<?php
include_once("../../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) || !isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php");
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
    exit;
}

$user_id = $_SESSION['user_id'];
$pagina = 1;
if (isset($_GET["pagina"])) {
    $pagina = $_GET["pagina"];
}

$selectNoleggio = "SELECT DATE(o.timestamp) AS dataNoleggio, CONCAT(i.comune, ' (', s.codice, ')') AS stazionePartenza, b.codice AS bicicletta
FROM operazione AS o
JOIN cliente AS c ON o.id_cliente = c.ID
LEFT JOIN stazione AS s ON o.id_stazione = s.ID
LEFT JOIN indirizzo AS i ON s.id_indirizzo = i.ID
LEFT JOIN bicicletta AS b ON o.id_bicicletta = b.ID
WHERE c.ID = ? AND o.tipo = 'noleggio'
ORDER BY o.timestamp DESC
LIMIT " . (($pagina - 1) * 10) . ", 10;";

$stmt = $conn->prepare($selectNoleggio);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultNoleggio = $stmt->get_result();
$resultNoleggio = $resultNoleggio->fetch_all(MYSQLI_ASSOC);

$selectRiconsegna = "SELECT DATE(o.timestamp) AS dataRiconsegna, CONCAT(i.comune, ' (', s.codice, ')') AS stazioneArrivo, o.kmEffettuati, o.tariffa
FROM operazione AS o
JOIN cliente AS c ON o.id_cliente = c.ID
LEFT JOIN stazione AS s ON o.id_stazione = s.ID
LEFT JOIN indirizzo AS i ON s.id_indirizzo = i.ID
LEFT JOIN bicicletta AS b ON o.id_bicicletta = b.ID
WHERE c.ID = ? AND o.tipo = 'riconsegna'
ORDER BY o.timestamp DESC
LIMIT " . (($pagina - 1) * 10) . ", 10;";

$stmt = $conn->prepare($selectRiconsegna);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultRiconsegna = $stmt->get_result();
$resultRiconsegna = $resultRiconsegna->fetch_all(MYSQLI_ASSOC);

$viaggi = array();

foreach ($resultNoleggio as $noleggio) {
    $viaggio = array();
    $viaggio['data'] = $noleggio['dataNoleggio'];
    $viaggio['stazionePartenza'] = $noleggio['stazionePartenza'] == null ? "Stazione non trovata" : $noleggio['stazionePartenza'];
    $viaggio['bicicletta'] = $noleggio['bicicletta'] == null ? "Bicicletta non trovata" : $noleggio['bicicletta'];
    $viaggio['stazioneArrivo'] = "Stazione non trovata";
    $viaggio['kmEffettuati'] = "Distanza non trovata";
    $viaggio['tariffa'] = "Tariffa non trovata";
    foreach ($resultRiconsegna as $riconsegna) {
        if ($noleggio['dataNoleggio'] == $riconsegna['dataRiconsegna']) {
            $viaggio['stazioneArrivo'] = $riconsegna['stazioneArrivo'] == null ? "Stazione non trovata" : $riconsegna['stazioneArrivo'];
            $viaggio['kmEffettuati'] = $riconsegna['kmEffettuati'];
            $viaggio['tariffa'] = $riconsegna['tariffa'];
            break;
        }
    }
    array_push($viaggi, $viaggio);
}
$result = array("viaggi" => $viaggi);

echo json_encode(array("status" => "success", "result" => $result));