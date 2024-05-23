<?php
include_once("../../Controllers/mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION["isLogged"]) || !$_SESSION["isLogged"]) || (!isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"])) {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_POST['regione']) || !isset($_POST['provincia']) || !isset($_POST['comune']) || !isset($_POST['cap']) || !isset($_POST['via']) || !isset($_POST['numeroCivico']) || !isset($_POST['numero_slot'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

$regione = $_POST['regione'];
$provincia = $_POST['provincia'];
$comune = $_POST['comune'];
$cap = $_POST['cap'];
$via = $_POST['via'];
$numeroCivico = $_POST['numeroCivico'];
$numero_slot = $_POST['numero_slot'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connessione fallita: " . $conn->connect_error));
    exit;
}

$conn->autocommit(false);
$conn->begin_transaction();

// controllo se l'indiriizzo è già presente
$check = "SELECT * FROM indirizzo WHERE regione = ? AND provincia = ? AND comune = ? AND cap = ? AND via = ? AND numeroCivico = ?";
$stmt = $conn->prepare($check);
$stmt->bind_param("sssisi", $regione, $provincia, $comune, $cap, $via, $numeroCivico);
$stmt->execute();
$result = $stmt->get_result();
$id_indirizzo = null;
if ($result->num_rows == 0) {
    // se non è presente lo inserisco
    $latLng = getLatLng($via . ', ' . $numeroCivico . ', ' . $cap . ', ' . $comune . ', ' . $provincia . ', ' . $regione);
    if ($latLng == null) {
        echo json_encode(array("status" => "errore", "message" => "Indirizzo non trovato"));
        exit;
    }
    $latitudine = $latLng['lat'];
    $longitudine = $latLng['lon'];

    $insert = "INSERT INTO indirizzo (regione, provincia, comune, cap, via, numeroCivico, latitudine, longitudine) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("sssisidd", $regione, $provincia, $comune, $cap, $via, $numeroCivico, $latitudine, $longitudine);
    $stmt->execute();
    $id_indirizzo = $conn->insert_id;
}
else {
    $id_indirizzo = $result->fetch_assoc()['ID'];
}

// vado a prendere il codice dell'ultima stazione inserita
$select = "SELECT codice FROM stazione ORDER BY codice DESC LIMIT 1";
$result = $conn->query($select);
$codice = 1;
if ($result->num_rows > 0) {
    $codice = $result->fetch_assoc()['codice'] + 1;
}

$insert = "INSERT INTO stazione (codice, id_indirizzo, numero_slot) VALUES (?, ?, ?)";
$stmt = $conn->prepare($insert);
$stmt->bind_param("iii", $codice, $id_indirizzo, $numero_slot);
$stmt->execute();

$conn->commit();

echo json_encode(array("status" => "success", "message" => "Stazione inserita correttamente"));

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