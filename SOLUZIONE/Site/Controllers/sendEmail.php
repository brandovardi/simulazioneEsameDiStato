<?php
include_once("../mysqliData/dataDB.php");

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['numeroTessera']) || !isset($_POST['email']) || !isset($_POST['subject']) || !$_POST['message']){
    header("Location: ../index.php");
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

include_once('./PHPMailer-master/src/PHPMailer.php');
include_once('./PHPMailer-master/src/Exception.php');
include_once('./PHPMailer-master/src/SMTP.php');

// recupero i dati dal form
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// creo un oggetto PHPMailer
$mail = new PHPMailer(true);

$conn = new mysqli($hostname, $username, $password, $database);
$select = "SELECT * FROM cliente WHERE email = ?";
$stmt = $conn->prepare($select);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows == 0) {
    echo json_encode(array("status" => "error", "message" => "Email non registrata"));
    exit;
}

$conn->close();

try {
    // imposto i parametri del server SMTP
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'maxboxex0909@gmail.com';
    $mail->Password = 'thqwczobhyckisje';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // imposto il mittente e il destinatario
    $mail->setFrom('maxboxex0909@gmail.com');
    $mail->FromName = 'Codice Tessera';
    $mail->addAddress($email);
    
    // imposto il subject e il body del messaggio
    $mail->isHTML(true);
    $mail->Subject = $subject;
    if (isset($_SESSION['numeroTessera']))
        $mail->Body = $message . '<h2><b>' . $_SESSION['numeroTessera'] .'</b></h2><br>';
    else
        $mail->Body = $message . '<br>';

    // invio l'email
    $mail->send();
    session_unset();
    $_SESSION["mail-sent"] = true;
    echo json_encode(array("status" => "success"));
} catch (Exception $e) {
    echo json_encode(array("status" => "error", "message" => "Errore nell'invio dell'email"));
}
