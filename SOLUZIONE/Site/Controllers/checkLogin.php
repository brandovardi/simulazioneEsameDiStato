<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['numeroTessera'])) {
    $conn = new mysqli("localhost", "root", "", "simulazione_esame");
    $conn->set_charset("utf8");
    if ($conn->connect_error) {
        echo json_encode(array("status" => "error", "message" => "Connessione al database fallita"));
        exit;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];
    $numeroTessera = $_POST['numeroTessera'];

    $stmt = null;
    $is_admin = false;
    if (str_contains($username, "_")) {
        if (empty($numeroTessera) || !is_numeric($numeroTessera)) {
            echo json_encode(array("status" => "error", "message" => "Inserire un numero di tessera valido"));
            exit;
        }

        $select = "SELECT * FROM cliente WHERE username = ? AND password = ? AND numeroTessera = ?";
        $stmt = $conn->prepare($select);
        $stmt->bind_param("sss", $username, $password, $numeroTessera);
    }
    else if (str_contains($username, ".")) {
        $select = "SELECT * FROM admin WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($select);
        $stmt->bind_param("ss", $username, $password);
        $is_admin = true;
    }
    else {
        echo json_encode(array("status" => "error", "message" => "Username non valido"));
        exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        if ($is_admin) {
            $_SESSION['is_admin'] = true;
        }
        $_SESSION['user_id'] = $result->fetch_assoc()['ID'];
        $_SESSION['isLogged'] = true;
        
        if (isset($_SESSION['mail-sent'])) {
            unset($_SESSION['mail-sent']);
        }
        echo json_encode(array("status" => "success"));
    } else {
        session_unset();
        echo json_encode(array("status" => "error", "message" => "Username, password o numero di tessera errati"));
    }

    exit;
}
