<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrati</title>
    <script src="../Js/jquery-3.7.1.min.js"></script>
    <script src="../Js/register.js"></script>
    <style>
        body {
            margin-top: 10%;
            text-align: center;
        }
    </style>
</head>

<body>

    <form action="../Controllers/checkRegistration.php" method="POST">
        <h4>Dati Utente</h4>
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required><br>
        <label for="cognome">Cognome</label>
        <input type="text" id="cognome" name="cognome" required><br>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required><br>
        <label for="email">E-mail</label>
        <input type="text" id="email" name="email" required><br>
        <label for="numeroCartaCredito">Numero della Carta di Credito</label>
        <input type="text" id="numeroCartaCredito" name="numeroCartaCredito" required><br>

        <h4>Indirizzo</h4>
        <label for="regione">Regione</label>
        <select name="regione" id="regione">
            <option value="Abruzzo">Abruzzo</option>
            <option value="Basilicata">Basilicata</option>
            <option value="Calabria">Calabria</option>
            <option value="Campania">Campania</option>
            <option value="Emilia-Romagna">Emilia-Romagna</option>
            <option value="Friuli-Venezia Giulia">Friuli-Venezia Giulia</option>
            <option value="Lazio">Lazio</option>
            <option value="Liguria">Liguria</option>
            <option value="Lombardia">Lombardia</option>
            <option value="Marche">Marche</option>
            <option value="Molise">Molise</option>
            <option value="Piemonte">Piemonte</option>
            <option value="Puglia">Puglia</option>
            <option value="Sardegna">Sardegna</option>
            <option value="Sicilia">Sicilia</option>
            <option value="Toscana">Toscana</option>
            <option value="Trentino-Alto Adige">Trentino-Alto Adige</option>
            <option value="Umbria">Umbria</option>
            <option value="Valle d'Aosta">Valle d'Aosta</option>
            <option value="Veneto">Veneto</option>
        </select><br>
        <label for="provincia">Provincia</label>
        <input type="text" id="provincia" name="provincia" minlength="2" required><br>
        <label for="citta">Città</label>
        <input type="text" id="citta" name="citta" minlength="2" required><br>
        <label for="via">Via</label>
        <input type="text" id="via" name="via" minlength="2" required><br>
        <label for="cap">CAP</label>
        <input type="number" id="cap" name="cap" min="10000" max="99999" required><br>
        <label for="numeroCivico">Numero Civico</label>
        <input type="number" id="numeroCivico" name="numeroCivico" min="1" required><br>

        <input type="submit" value="Registrati">
    </form>

    <div id="error"></div>

    Hai già un account? <a href="login.php">Accedi!</a>

</body>

</html>