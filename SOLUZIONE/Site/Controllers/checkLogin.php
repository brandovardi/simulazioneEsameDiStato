<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['numeroTessera'])) {
    $conn = new mysqli("localhost", "root", "", "simulazione_esame");
    $conn->set_charset("utf8");

    $username = $_POST['username'];
    $password = hash('sha256', $_POST['password']);
    $numeroTessera = $_POST['numeroTessera'];

    $stmt = null;
    if (str_contains($username, "_")) {
        if (empty($numeroTessera) || !is_numeric($numeroTessera)) {
            echo json_encode(array("status" => "error", "message" => "Inserire un numero di tessera valido"));
            exit;
        }

        $select = "SELECT * FROM cliente WHERE username = ? AND password = ? AND numeroTessera = ?";
        $stmt = $conn->prepare($select);
        $stmt->bind_param("ssi", $username, $password, $numeroTessera);
    }
    else if (str_contains($username, ".")) {
        $select = "SELECT * FROM admin WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($select);
        $stmt->bind_param("ss", $username, $password);
    }
    else {
        echo json_encode(array("status" => "error", "message" => "Username non valido"));
        exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        echo json_encode(array("status" => "success"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Username o password errati"));
    }
    exit;
}