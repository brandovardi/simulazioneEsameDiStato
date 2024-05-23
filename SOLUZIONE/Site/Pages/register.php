<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['username']) || (isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == true)) {
    header("Location: ./Customers/home.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrati</title>
    <script src="../Js/Cdn/Jquery/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <link href="../Css/Cdn/Bootstrap/bootstrap.min.css" rel="stylesheet">
    <script src="../Js/Cdn/Bootstrap/bootstrap.min.js"></script>

    <script src="../Js/request.js" defer></script>
    <script src="../Js/register.js" defer></script>
    <script src="../Js/template.js"></script>
    <script src="../Js/Secure/crypto.js" defer></script>
    <style>
        /* For Firefox */
        input[type=number] {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }
    </style>
</head>

<body id="body-login">
    
    <nav>
        <script>
            generateNavBar("guest");
        </script>
    </nav>

    <div class="loader-container" id="loaderContainer">
        <div class="loader"></div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <h2 class="text-center">Registrati</h2>

                            <div class="row">
                                <!-- Dati Utente -->
                                <div class="col-md-6">
                                    <h4>Dati Personali</h4>
                                    <div class="form-group">
                                        <label for="nome">Nome</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="cognome">Cognome</label>
                                        <input type="text" class="form-control" id="cognome" name="cognome" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">E-mail</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="numeroCartaCredito">Numero della Carta di Credito</label>
                                        <input type="text" class="form-control" id="numeroCartaCredito"
                                            name="numeroCartaCredito" required>
                                    </div>
                                </div>

                                <!-- Indirizzo -->
                                <div class="col-md-6">
                                    <h4>Indirizzo</h4>
                                    <div class="form-group">
                                        <label for="regione">Regione</label>
                                        <select class="form-control" name="regione" id="regione"></select>
                                    </div>
                                    <div class="form-group">
                                        <label for="provincia">Provincia</label>
                                        <select class="form-control" name="provincia" id="provincia"></select>
                                    </div>
                                    <div class="form-group">
                                        <label for="comune">Comune</label>
                                        <select class="form-control" name="citta" id="comune"></select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cap">CAP</label>
                                        <input type="number" class="form-control" id="cap" name="cap" required disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="via">Via</label>
                                        <input type="text" class="form-control" id="via" name="via" minlength="2"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="numeroCivico">Numero Civico</label>
                                        <input type="number" class="form-control" id="numeroCivico" name="numeroCivico"
                                            min="1" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt-4"
                                id="btnRegister">Registrati</button>
                        </form>

                        <div id="error" class="mt-3 text-danger">
                            <?php
                            // Messaggio di errore se presente
                            ?>
                        </div>

                        <p class="mt-3 text-center">Hai già un account? <a href="login.php">Accedi!</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>