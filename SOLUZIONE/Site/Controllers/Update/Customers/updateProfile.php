<?php
include_once ("../../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || !isset($_SESSION['isLogged']) || !$_SESSION['isLogged'] || !isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php");
    exit;
}

if (
    !isset($_POST['nome']) || !isset($_POST['cognome'])
    || !isset($_POST['regione']) || !isset($_POST['provincia']) || !isset($_POST['comune'])
    || !isset($_POST['cap']) || !isset($_POST['via']) || !isset($_POST['numeroCivico'])
) {
    echo json_encode(array("status" => "errore", "message" => "Parametri mancanti"));
    exit;
}

$nome = $_POST['nome'];
$cognome = $_POST['cognome'];

$regione = strtolower($_POST['regione']);
if (str_contains('-', $regione)) {
    $split = explode('-', $regione);
    $regione = ucfirst($split[0]) . "-" . ucfirst($split[1]);
} else
    $regione = ucfirst($regione);
$provincia = strtoupper($_POST['provincia']);
if (strlen($provincia) != 2) {
    echo json_encode(array("status" => "error", "message" => "La provincia deve essere di 2 lettere"));
    exit;
}
$comune = $_POST['comune'];
$via = $_POST['via'];
$cap = $_POST['cap'];
if (!is_numeric($cap) || strlen($cap) != 5) {
    echo json_encode(array("status" => "error", "message" => "Il CAP deve essere un numero di 5 cifre"));
    exit;
}
$numeroCivico = $_POST['numeroCivico'];
if (!is_numeric($numeroCivico)) {
    echo json_encode(array("status" => "error", "message" => "Il numero civico deve essere un numero"));
    exit;
}

if (
    empty($nome) || empty($cognome)
    || empty($regione) || empty($provincia) || empty($comune)
    || empty($cap) || empty($via) || empty($numeroCivico)
) {
    echo json_encode(array("status" => "errore", "message" => "Parametri vuoti"));
    exit;
}

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
    exit;
}
$conn->autocommit(false);
$conn->begin_transaction();

$update = "UPDATE cliente SET nome = ?, cognome = ? WHERE ID = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("ssi", $nome, $cognome, $_SESSION['user_id']);
$stmt->execute();

if ($stmt->affected_rows == 0) {
    $conn->rollback();
    echo json_encode(array("status" => "errore", "message" => "Errore durante l'aggiornamento del profilo"));
    exit;
}
$select = "SELECT id_indirizzo FROM cliente WHERE ID = ?";
$stmt = $conn->prepare($select);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$id_indirizzo = $result->fetch_assoc()['id_indirizzo'];

$latlng = getLatLng($regione . ', ' . $provincia . ', ' . $comune . ', ' . $via . ', ' . $cap . ', ' . $numeroCivico);
if ($latlng == null) {
    $conn->rollback();
    echo json_encode(array("status" => "errore", "message" => "Indirizzo non trovato"));
    exit;
}

$update = "UPDATE indirizzo SET regione = ?, provincia = ?, comune = ?, cap = ?, via = ?, numeroCivico = ?, latitudine = ?, longitudine = ? WHERE ID = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("sssisiddi", $regione, $provincia, $comune, $cap, $via, $numeroCivico, $latlng['lat'], $latlng['lon'], $id_indirizzo);
$stmt->execute();

if ($stmt->affected_rows == 1) {
    $conn->commit();
    echo json_encode(array("status" => "success", "message" => "Profilo aggiornato correttamente"));
    exit;
} else {
    $conn->rollback();
    echo json_encode(array("status" => "errore", "message" => "Errore durante l'aggiornamento dell'indirizzo"));
    exit;
}


function getLatLng($data)
{
    $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' . urlencode($data);

    // devo dirgli che sto mandando la richiesta da un browser perché altrimenti dà errore: "HTTP request failed! HTTP/1.1 403 Forbidden"
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:90.0) Gecko/20100101 Firefox/90.0\r\n"
        ]
    ]);

    // gli passo l'header appena modificato
    $response = file_get_contents($url, false, $context);

    $data = json_decode($response, true);
    if (!empty($data) && is_array($data)) {
        $lat = $data[0]['lat'];
        $lon = $data[0]['lon'];
        return array('lat' => $lat, 'lon' => $lon);
    } else {
        return null;
    }
}