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
    <title>Accedi</title>
    <link rel="stylesheet" href="../Css/template.css">
    <script src="../Js/Cdn/Jquery/jquery-3.7.1.min.js"></script>
    <script src="../Js/request.js"></script>
    <script src="../Js/Secure/crypto.js"></script>
    <script src="../Js/login.js"></script>
</head>

<body>

    <div class="container">
        <form id="loginForm" action="../Controllers/checkLogin.php" method="POST" class="form">
            <h2>Accedi</h2>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="numeroTessera">Numero della tessera</label>
                <input type="password" id="numeroTessera" name="numeroTessera">
            </div>
            <button type="submit">Accedi</button>
        </form>

        <div id="error" class="mt-3"></div>

        <?php

        if (isset($_SESSION['mail-sent']) && $_SESSION['mail-sent'] == true) {
            echo "<div id='error'>Il numero di tessera è stato inviato alla tua email</div>";
        }

        ?>

        <p>Non hai ancora un'account? <a href="register.php">Registrati!</a></p>
    </div>

</body>

</html>