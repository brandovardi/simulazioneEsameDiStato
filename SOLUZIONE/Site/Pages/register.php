<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['username']) || (isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == true)) {
    header("Location: ./Customers/home_c.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrati</title>
    <link rel="stylesheet" href="../Css/register.css"> <!-- Link to your CSS file -->
    <script src="../Js/jquery-3.7.1.min.js"></script>
    <script src="../Js/request.js" defer></script>
    <script src="../Js/province.js" defer></script>
    <script src="../Js/register.js" defer></script>
</head>

<body>

    <div class="container">
        <form method="POST" class="form">
            <h2>Registrati</h2>

            <!-- Dati Utente -->
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="cognome">Cognome</label>
                <input type="text" id="cognome" name="cognome" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="numeroCartaCredito">Numero della Carta di Credito</label>
                <input type="text" id="numeroCartaCredito" name="numeroCartaCredito" required>
            </div>

            <!-- Indirizzo -->
            <h4>Indirizzo</h4>
            <div class="form-group">
                <label for="regione">Regione</label>
                <select name="regione" id="regione"></select>
            </div>
            <div class="form-group">
                <label for="provincia">Provincia</label>
                <select name="provincia" id="provincia"></select>
            </div>
            <div class="form-group">
                <label for="citta">Città</label>
                <input type="text" id="citta" name="citta" minlength="2" required>
            </div>
            <div class="form-group">
                <label for="via">Via</label>
                <input type="text" id="via" name="via" minlength="2" required>
            </div>
            <div class="form-group">
                <label for="cap">CAP</label>
                <input type="number" id="cap" name="cap" min="10000" max="99999" required>
            </div>
            <div class="form-group">
                <label for="numeroCivico">Numero Civico</label>
                <input type="number" id="numeroCivico" name="numeroCivico" min="1" required>
            </div>

            <button type="submit">Registrati</button>
        </form>

        <div id="error" class="mt-3"></div>

        <p>Hai già un account? <a href="login.php">Accedi!</a></p>
    </div>

</body>

</html>