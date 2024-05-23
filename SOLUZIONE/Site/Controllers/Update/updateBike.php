<?php
include_once ("../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || !isset($_SESSION['isLogged']) || !$_SESSION['isLogged'] || !isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    header("Location: ../../index.php");
    exit;
}

if (
    !isset($_POST['codice']) || !isset($_POST['manutenzione']) || !isset($_POST['gps'])
    || !isset($_POST['rfid']) || !isset($_POST['codiceStazione'])
) {
    echo json_encode(array("status" => "errore", "message" => "Parametri mancanti"));
    exit;
}

$codice = $_POST['codice'];
$manutenzione = $_POST['manutenzione'];
$gps = $_POST['gps'];
$rfid = $_POST['rfid'];
$codiceStazione = $_POST['codiceStazione'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
    exit;
}

$conn->autocommit(false);
$conn->begin_transaction();

$id_stazione = null;
$id_posizione = null;
if ($codiceStazione != null) {
    // controllo che la stazione esista
    $select = "SELECT * FROM stazione WHERE codice = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("i", $codiceStazione);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_stazione = $row['ID'];
        $id_posizione = $row['id_indirizzo'];
    } else {
        echo json_encode(array("status" => "errore", "message" => "Stazione non trovata"));
        exit;
    }
}

// aggiorno la Bicicletta
$update = "UPDATE bicicletta SET manutenzione = ?, GPS = ?, RFID = ?, id_stazione = ?, id_posizione = ? WHERE codice = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("ississ", $manutenzione, $gps, $rfid, $id_stazione, $id_posizione, $codice);
$stmt->execute();

if ($stmt->affected_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Errore nell'aggiornamento della bicicletta"));
    exit;
}

$conn->commit();

echo json_encode(array("status" => "success", "message" => "Bicicletta aggiornata correttamente"));

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
