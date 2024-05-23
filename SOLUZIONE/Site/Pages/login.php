<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['username']) || (isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == true)) {
    if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == true) {
        header("Location: ./Admin/home.php");
    } else {
        header("Location: ./Customers/home.php");
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi</title>
    <script src="../Js/Cdn/Jquery/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <link href="../Css/Cdn/Bootstrap/bootstrap.min.css" rel="stylesheet">
    <script src="../Js/Cdn/Bootstrap/bootstrap.min.js"></script>

    <script src="../Js/request.js"></script>
    <script src="../Js/Secure/crypto.js"></script>
    <script src="../Js/login.js"></script>
    <script src="../Js/template.js"></script>
</head>

<body>
    
    <nav>
        <script>
            generateNavBar("guest");
        </script>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form id="loginForm" action="../Controllers/checkLogin.php" method="POST">
                            <h2 class="text-center">Accedi</h2>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="numeroTessera">Numero della tessera</label>
                                <input type="password" class="form-control" id="numeroTessera" name="numeroTessera">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Accedi</button>
                        </form>

                        <div id="error" class="mt-3 text-danger">
                            <?php
                            if (isset($_SESSION['mail-sent']) && $_SESSION['mail-sent'] == true) {
                                echo "Il numero di tessera è stato inviato alla tua email";
                            }
                            ?>
                        </div>

                        <p class="mt-3 text-center">Non hai ancora un account? <a href="register.php">Registrati!</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>