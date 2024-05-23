<?php
include_once ("../mysqliData/dataDB.php");

if (!isset($_GET['numeroTessera']) || !isset($_GET['tipoOperazione']) || !isset($_GET['codiceBici']) || !isset($_GET['codiceStazione'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

$numeroTessera = $_GET['numeroTessera'];
$tipoOperazione = $_GET['tipoOperazione'];
$codiceBici = $_GET['codiceBici'];
$codiceStazione = $_GET['codiceStazione'];

$conn = new mysqli($hostname, $username, $password, $database_simulazione);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(array("status" => "errore", "message" => "Connessione al database fallita"));
    exit;
}

$conn->autocommit(FALSE);
$conn->begin_transaction();

if ($tipoOperazione == "noleggio") {
    // inserisco l'operazione di noleggio
    $insert = "INSERT INTO operazione (id_cliente, id_bicicletta, id_stazione, tipo, timestamp, tariffa, kmEffettuati)
    VALUES (
        (SELECT ID FROM cliente WHERE numeroTessera = ?),
        (SELECT ID FROM bicicletta WHERE codice = ?),
        (SELECT ID FROM stazione WHERE codice = ?),
        ?,
        current_timestamp(),
        0,
        0
    )";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("ssis", $numeroTessera, $codiceBici, $codiceStazione, $tipoOperazione);
    $stmt->execute();
    $id_operazione = $conn->insert_id;
    $stmt->close();

    // rimuovo la bicicletta dalla stazione
    $update = "UPDATE bicicletta SET id_stazione = NULL WHERE codice = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("s", $codiceBici);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo json_encode(array("status" => "success", "message" => "Noleggio effettuato"));
} else if ($tipoOperazione == "riconsegna") {
    // recupero i dati del noleggio per calcolare la tariffa e la distanza percorsa
    $select = "SELECT ID, kmEffettuati, DATE_FORMAT(timestamp, '%H:%i') AS oraPartenza
    FROM operazione
    WHERE id_cliente = (SELECT ID FROM cliente WHERE numeroTessera = ?)
    AND id_bicicletta = (SELECT ID FROM bicicletta WHERE codice = ?)
    AND tipo = 'noleggio'";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("ss", $numeroTessera, $codiceBici);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($result->num_rows == 0) {
        echo json_encode(array("status" => "error", "message" => "Noleggio non trovato"));
        exit;
    }
    $kmEffettuati = $row['kmEffettuati'];
    echo "kmEffettuati: " . $kmEffettuati;
    $oraPartenza = $row['oraPartenza'];
    $id_operazione = $row['ID'];
    $stmt->close();

    // calcolo la tariffa in base al tempo trascorso
    $tempoTrascorso = strtotime(date('H:i')) - strtotime($oraPartenza);
    $tariffa = $tempoTrascorso * 0.01;

    // inserisco l'operazione di riconsegna
    $insert = "INSERT INTO operazione (id_cliente, id_bicicletta, id_stazione, tipo, timestamp, tariffa, kmEffettuati)
    VALUES (
        (SELECT ID FROM cliente WHERE numeroTessera = ?),
        (SELECT ID FROM bicicletta WHERE codice = ?),
        (SELECT ID FROM stazione WHERE codice = ?),
        ?,
        current_timestamp(),
        ?,
        ?
    )";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("ssisii", $numeroTessera, $codiceBici, $codiceStazione, $tipoOperazione, $tariffa, $kmEffettuati);
    $stmt->execute();
    $stmt->close();
    
    // aggiorno i dati del noleggio
    $update = "UPDATE operazione
    SET kmEffettuati = NULL, tariffa = NULL
    WHERE id_cliente = (SELECT ID FROM cliente WHERE numeroTessera = ?)
    AND id_bicicletta = (SELECT ID FROM bicicletta WHERE codice = ?)
    AND tipo = 'noleggio'";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ss", $numeroTessera, $codiceBici);
    $stmt->execute();
    $stmt->close();

    // aggiorno la stazione della bicicletta
    $update = "UPDATE bicicletta
    SET id_stazione = (SELECT ID FROM stazione WHERE codice = ?)
    WHERE codice = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ss", $codiceStazione, $codiceBici);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo json_encode(array("status" => "success", "message" => "Riconsegna effettuata"));
} else {
    echo json_encode(array("status" => "error", "message" => "Tipo operazione non valido"));
}
