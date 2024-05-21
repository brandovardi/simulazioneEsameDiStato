<?php
include_once("../../Controllers/mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username']) || (!isset($_SESSION["isLogged"]) || !$_SESSION["isLogged"]) || (!isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['codice'])) {
    echo json_encode(array("status" => "error", "message" => "Parametri mancanti"));
    exit;
}

echo json_encode(array("status" => "ok"));