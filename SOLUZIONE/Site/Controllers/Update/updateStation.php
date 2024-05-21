<?php
include_once ("../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged'] || !isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    echo json_encode(array("status" => "errore", "message" => "Utente non autenticato"));
    exit;
}

if (
    !isset($_POST['codice']) || !isset($_POST['regione']) || !isset($_POST['provincia'])
    || !isset($_POST['comune']) || !isset($_POST['cap']) || !isset($_POST['via'])
    || !isset($_POST['numeroCivico']) || !isset($_POST['numero_slot'])
) {
    echo json_encode(array("status" => "errore", "message" => "Parametri mancanti"));
    exit;
}

$codice = $_POST['codice'];
// indirizzo
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
$numero_slot = $_POST['numero_slot'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
    exit;
}

// controllo che la stazione esista
$select = "SELECT * FROM stazione WHERE codice = ?";
$stmt = $conn->prepare($select);
$stmt->bind_param("i", $codice);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(array("status" => "errore", "message" => "Stazione non trovata"));
    exit;
}

// controllo se l'indirizzo esiste già
$select = "SELECT * FROM indirizzo WHERE regione = ? AND provincia = ? AND comune = ? AND cap = ? AND via = ? AND numeroCivico = ?";
$stmt = $conn->prepare($select);
$stmt->bind_param("sssisi", $regione, $provincia, $comune, $cap, $via, $numeroCivico);
$stmt->execute();
$result = $stmt->get_result();

// se l'indirizzo esiste già allora assegno l'id dell'indirizzo esistente alla stazione
$id_indirizzo = null;
if ($result->num_rows > 0) {
    $id_indirizzo = $result->fetch_assoc()['ID'];
} else {
    // altrimenti inserisco l'indirizzo nel database
    $latLng = getLatLng($via . ', ' . $numeroCivico . ', ' . $cap . ', ' . $comune . ', ' . $provincia . ', ' . $regione);
    if ($latLng == null) {
        echo json_encode(array("status" => "errore", "message" => "Indirizzo non trovato"));
        exit;
    }

    // inserisco l'indirizzo
    $insert = "INSERT INTO indirizzo (regione, provincia, comune, cap, via, numeroCivico, latitudine, longitudine) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("sssisidd", $regione, $provincia, $comune, $cap, $via, $numeroCivico, $latLng['lat'], $latLng['lon']);
    $stmt->execute();
    $id_indirizzo = $conn->insert_id;
}

// aggiorno la stazione
$update = "UPDATE stazione SET id_indirizzo = ?, numero_slot = ? WHERE codice = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("iii", $id_indirizzo, $numero_slot, $codice);
$stmt->execute();

echo json_encode(array("status" => "success", "message" => "Stazione aggiornata correttamente"));

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