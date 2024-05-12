<?php

if (!isset($_SESSION)) {
    session_start();
}

if (
    isset($_POST['nome']) && isset($_POST['cognome']) && isset($_POST['username'])
    && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['numeroCartaCredito'])
    && isset($_POST['regione']) && isset($_POST['provincia']) && isset($_POST['comune'])
    && isset($_POST['via']) && isset($_POST['cap']) && isset($_POST['numeroCivico'])
    // controllo se i campi sono vuoti
    && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['nome'])
    && !empty($_POST['cognome']) && !empty($_POST['email']) && !empty($_POST['numeroCartaCredito'])
    && !empty($_POST['regione']) && !empty($_POST['provincia']) && !empty($_POST['comune'])
    && !empty($_POST['via']) && !empty($_POST['cap']) && !empty($_POST['numeroCivico'])
) {

    // connessione al db
    $conn = new mysqli("localhost", "root", "", "simulazione_esame");
    $conn->set_charset("utf8");
    if ($conn->connect_error) {
        echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
        exit;
    }

    // indirizzo
    $regione = strtolower($_POST['regione']);
    if (str_contains('-', $regione)) {
        $split = explode('-', $regione);
        $regione = ucfirst($split[0]) . "-" . ucfirst($split[1]);
    } else
        $regione = ucfirst($regione);
    $provincia = strtoupper($_POST['provincia']);
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
    $latLng = getLatLng($via . ', ' . $numeroCivico . ', ' . $cap . ', ' . $comune . ', ' . $provincia . ', ' . $regione);

    // dati anagrafici
    $nome = ucfirst(strtolower($_POST['nome']));
    $cognome = ucfirst(strtolower($_POST['cognome']));
    $cognome = ucfirst($cognome);
    $username = strtolower($_POST['username']);
    if (!str_contains($username, "_")) {
        echo json_encode(array("status" => "error", "message" => "L'username deve contenere il carattere '_'"));
        exit;
    }
    $password = $_POST['password'];
    $email = $_POST['email'];
    if (!checkEmail($email)) {
        echo json_encode(array("status" => "error", "message" => "Email non valida"));
        exit;
    }
    $numeroCartaCredito = $_POST['numeroCartaCredito'];

    // faccio partire una transaction
    $conn->begin_transaction();

    try {
        // controllo se l'email è già in uso
        $select = "SELECT * FROM cliente WHERE email = ? OR username = ?";
        $stmt = $conn->prepare($select);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(array("status" => "error", "message" => "Utente già registrato"));
            exit;
        }
        // controllo la regex per la carta di credito
        if (!preg_match("/^\d{4}-\d{4}-\d{4}-\d{4}$/", $numeroCartaCredito)) {
            echo json_encode(array("status" => "error", "message" => "Il numero della carta di credito deve essere un numero di 16 cifre"));
            exit;
        }

        // controllo se l'indirizzo è già presente
        $select = "SELECT * FROM indirizzo WHERE regione = ? AND provincia = ? AND comune = ? AND cap = ? AND via = ? AND numeroCivico = ?";
        $stmt = $conn->prepare($select);
        $stmt->bind_param("sssisi", $regione, $provincia, $comune, $cap, $via, $numeroCivico);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_indirizzo = $row['ID'];
        } else {
            // altrimenti lo inserisco
            $insert = "INSERT INTO indirizzo (regione, provincia, comune, cap, via, numeroCivico, latitudine, longitudine) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("sssisidd", $regione, $provincia, $comune, $cap, $via, $numeroCivico, $latLng['lat'], $latLng['lon']);
            $stmt->execute();

            // vado a prendere l'id dell'indirizzo appena inserito
            $select = "SELECT ID FROM indirizzo ORDER BY ID DESC LIMIT 1";
            $result = $conn->query($select);
            $row = $result->fetch_assoc();
            $id_indirizzo = $row['ID'];
        }

        // vado a prendere l'ultimo valore di tessera inserito e lo incremento di 1
        $select = "SELECT numeroTessera FROM cliente ORDER BY numeroTessera DESC LIMIT 1";
        $result = $conn->query($select);
        $row = $result->fetch_assoc();
        $numeroTessera = $row['numeroTessera'] + 1;
        $numeroTessera = str_pad($numeroTessera, 7, "0", STR_PAD_LEFT);

        // inserisco il cliente
        $insert = "INSERT INTO cliente (nome, cognome, username, password, id_indirizzo, email, numeroCartaCredito, numeroTessera) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("ssssisss", $nome, $cognome, $username, $password, $id_indirizzo, $email, $numeroCartaCredito, $numeroTessera);
        $stmt->execute();

        // committo la transaction
        $conn->commit();
        $conn->close();

        $_SESSION['numeroTessera'] = $numeroTessera;
        $message = "<h1>Benvenuto/a " . $nome . " " . $cognome . "!</h1><br>Ecco a te il numero di tessera per effettuare la login: ";
        echo json_encode(array("status" => "success", "message" => $message));
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(array("status" => "error", "message" => "Errore nella registrazione: "));
        exit;
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Compila tutti i campi"));
    exit;
}

function checkEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
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